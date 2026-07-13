<?php

namespace Tests\Feature;

use App\Models\DiagnosticRun;
use App\Models\Screen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DiagnosticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_screen_without_web_visible_tag_reports_visibility_blocker(): void
    {
        $screen = Screen::create([
            'xibo_display_id' => 4,
            'public_code' => 'ELIXE-004',
            'display_name' => 'ELIXE-004',
            'public_name' => 'Casa Marino',
            'municipality' => 'Valdovino',
            'province' => 'A Coruna',
            'latitude' => 43.5832513,
            'longitude' => -8.1820912,
            'location_type' => 'bar',
            'location_sector' => 'hosteleria',
            'web_visible_from_xibo' => false,
            'commercial_status' => 'disponible',
            'logged_in' => true,
            'licensed' => true,
            'media_inventory_status' => true,
            'synced_at' => now(),
        ]);
        $screen->tags()->create(['tag' => 'loc_tipo', 'value' => 'bar']);
        $screen->tags()->create(['tag' => 'loc_sector', 'value' => 'hosteleria']);
        $screen->tags()->create(['tag' => 'com_estado', 'value' => 'disponible']);

        $this->assertContains('web_visible', $screen->fresh('tags')->missingFields());
        $this->assertContains('Falta el tag web_visible=true en Xibo.', $screen->fresh('tags')->publicVisibilityBlockers());
        $this->assertSame(0, Screen::publiclyVisible()->count());
    }

    public function test_admin_can_run_diagnostics_and_see_public_map_blockers(): void
    {
        $admin = User::factory()->create();
        config([
            'services.xibo.base_url' => 'https://cms.example.test/api',
            'services.xibo.client_id' => 'client-id',
            'services.xibo.client_secret' => 'client-secret',
        ]);
        Http::fake([
            'https://cms.example.test/api/authorize/access_token' => Http::response(['access_token' => 'token', 'expires_in' => 3600]),
            'https://cms.example.test/api/about' => Http::response(['version' => '4.4.0']),
            'https://cms.example.test/api/clock' => Http::response(['time' => '20:06 CEST']),
        ]);

        Screen::create([
            'xibo_display_id' => 4,
            'public_code' => 'ELIXE-004',
            'display_name' => 'ELIXE-004',
            'public_name' => 'Casa Marino',
            'latitude' => 43.5832513,
            'longitude' => -8.1820912,
            'location_type' => 'bar',
            'location_sector' => 'hosteleria',
            'web_visible_from_xibo' => false,
            'commercial_status' => 'disponible',
            'synced_at' => now(),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.diagnostics.run'))
            ->assertRedirect();

        $run = DiagnosticRun::firstOrFail();

        $this->assertSame('warning', $run->status);
        $this->assertSame(0, $run->checks['public_map']['details']['visible_screens']);
        $this->assertSame('Falta el tag web_visible=true en Xibo.', $run->checks['public_map']['details']['blocked_screens'][0]['blockers'][0]);
    }
}
