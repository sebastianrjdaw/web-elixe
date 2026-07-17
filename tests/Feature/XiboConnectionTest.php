<?php

namespace Tests\Feature;

use App\Models\Screen;
use App\Models\SyncRun;
use App\Services\Xibo\SyncDisplays;
use App\Services\Xibo\XiboService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class XiboConnectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_xibo_test_connection_command_succeeds_with_valid_api_responses(): void
    {
        Cache::forget('xibo.access_token');
        config([
            'services.xibo.base_url' => 'https://cms.example.test/api',
            'services.xibo.client_id' => 'client-id',
            'services.xibo.client_secret' => 'client-secret',
        ]);

        Http::fake([
            'https://cms.example.test/api/authorize/access_token' => Http::response([
                'access_token' => 'token',
                'expires_in' => 3600,
            ]),
            'https://cms.example.test/api/about' => Http::response(['version' => '4.4.0']),
            'https://cms.example.test/api/clock' => Http::response(['time' => '19:54 CEST']),
        ]);

        $this->artisan('xibo:test-connection')
            ->expectsOutput('Xibo connection OK.')
            ->assertSuccessful();
    }

    public function test_xibo_test_connection_command_fails_without_leaking_client_secret(): void
    {
        Cache::forget('xibo.access_token');
        config([
            'services.xibo.base_url' => 'https://cms.example.test/api',
            'services.xibo.client_id' => 'client-id',
            'services.xibo.client_secret' => 'super-secret-value',
        ]);

        Http::fake([
            'https://cms.example.test/api/authorize/access_token' => Http::response(['error' => 'invalid_client'], 401),
        ]);

        $this->artisan('xibo:test-connection')
            ->expectsOutput('Xibo authorization failed with status 401.')
            ->assertFailed();
    }

    public function test_failed_xibo_sync_is_recorded_without_crashing(): void
    {
        $sync = new SyncDisplays(new class extends XiboService
        {
            public function displays(int $start = 0, int $length = 100): array
            {
                throw new RuntimeException('Xibo timeout');
            }
        });

        $run = $sync->run();

        $this->assertSame('failed', $run->status);
        $this->assertSame('Xibo timeout', $run->error_message);
        $this->assertDatabaseHas('sync_runs', [
            'source' => 'xibo',
            'status' => 'failed',
            'error_message' => 'Xibo timeout',
        ]);
        $this->assertSame(1, SyncRun::count());
    }

    public function test_xibo_service_fetches_every_display_page(): void
    {
        Cache::forget('xibo.access_token');
        config([
            'services.xibo.base_url' => 'https://cms.example.test/api',
            'services.xibo.client_id' => 'client-id',
            'services.xibo.client_secret' => 'client-secret',
        ]);
        Http::fake(function ($request) {
            if (str_contains($request->url(), '/authorize/access_token')) {
                return Http::response(['access_token' => 'token', 'expires_in' => 3600]);
            }

            parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

            return Http::response(((int) ($query['start'] ?? 0)) === 0
                ? array_fill(0, 100, ['displayId' => 1])
                : [['displayId' => 101]]);
        });

        $this->assertCount(101, app(XiboService::class)->allDisplays());
    }

    public function test_sync_removes_tags_that_were_deleted_in_xibo(): void
    {
        $screen = Screen::create([
            'xibo_display_id' => 4,
            'public_code' => 'ELIXE-004',
            'display_name' => 'Pantalla anterior',
        ]);
        $screen->tags()->create(['tag' => 'obsolete', 'value' => 'true']);

        $sync = new SyncDisplays(new class extends XiboService
        {
            public function allDisplays(int $pageSize = 100): array
            {
                return [[
                    'displayId' => 4,
                    'display' => 'Pantalla actualizada',
                    'tags' => [],
                ]];
            }
        });

        $run = $sync->run();

        $this->assertSame('success', $run->status);
        $this->assertDatabaseMissing('screen_tags', ['screen_id' => $screen->id, 'tag' => 'obsolete']);
    }

    public function test_sync_hides_a_public_screen_that_no_longer_exists_in_xibo(): void
    {
        $screen = Screen::create([
            'xibo_display_id' => 99,
            'public_code' => 'ELIXE-099',
            'display_name' => 'Pantalla retirada',
            'latitude' => 43.3,
            'longitude' => -8.4,
            'web_visible_from_xibo' => true,
            'commercial_status' => 'disponible',
        ]);

        $sync = new SyncDisplays(new class extends XiboService
        {
            public function allDisplays(int $pageSize = 100): array
            {
                return [];
            }
        });

        $run = $sync->run();

        $this->assertSame('success', $run->status);
        $this->assertFalse($screen->fresh()->web_visible_from_xibo);
        $this->assertSame('retirada', $screen->fresh()->commercial_status);
    }
}
