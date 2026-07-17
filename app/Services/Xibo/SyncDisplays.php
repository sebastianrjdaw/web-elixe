<?php

namespace App\Services\Xibo;

use App\Models\Screen;
use App\Models\SyncRun;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

class SyncDisplays
{
    public function __construct(private readonly XiboService $xibo) {}

    public function run(?int $triggeredByUserId = null): SyncRun
    {
        $lock = Cache::lock('xibo.sync-displays', 300);

        if (! $lock->get()) {
            return SyncRun::create([
                'source' => 'xibo',
                'status' => 'skipped',
                'started_at' => now(),
                'finished_at' => now(),
                'error_message' => 'Ya hay una sincronizacion Xibo en curso.',
                'triggered_by_user_id' => $triggeredByUserId,
            ]);
        }

        $run = SyncRun::create([
            'source' => 'xibo',
            'status' => 'running',
            'started_at' => now(),
            'triggered_by_user_id' => $triggeredByUserId,
        ]);

        $created = 0;
        $updated = 0;
        $skipped = 0;

        try {
            $displays = $this->xibo->allDisplays();
            $seenDisplayIds = [];

            foreach ($displays as $display) {
                $displayId = (int) Arr::get($display, 'displayId');

                if ($displayId <= 0) {
                    $skipped++;

                    continue;
                }

                $seenDisplayIds[] = $displayId;

                $tags = $this->xibo->normalizeTags($display);

                DB::transaction(function () use ($displayId, $display, $tags, &$created, &$updated): void {
                    $screen = Screen::updateOrCreate(
                        ['xibo_display_id' => $displayId],
                        $this->screenAttributes($display, $tags)
                    );

                    $changed = $screen->wasRecentlyCreated || $screen->wasChanged();

                    if ($tags === []) {
                        $changed = $screen->tags()->exists() || $changed;
                        $screen->tags()->delete();
                    } else {
                        $changed = $screen->tags()->whereNotIn('tag', array_keys($tags))->delete() > 0 || $changed;

                        foreach ($tags as $tag => $value) {
                            $screenTag = $screen->tags()->updateOrCreate(['tag' => $tag], ['value' => $value]);
                            $changed = $screenTag->wasRecentlyCreated || $screenTag->wasChanged() || $changed;
                        }
                    }

                    if ($screen->wasRecentlyCreated) {
                        $created++;
                    } elseif ($changed) {
                        $updated++;
                    }
                });
            }

            $updated += Screen::query()
                ->whereNotIn('xibo_display_id', $seenDisplayIds)
                ->where('web_visible_from_xibo', true)
                ->update([
                    'web_visible_from_xibo' => false,
                    'commercial_status' => 'retirada',
                    'synced_at' => now(),
                ]);

            $run->update([
                'status' => 'success',
                'finished_at' => now(),
                'records_found' => count($displays),
                'records_created' => $created,
                'records_updated' => $updated,
                'records_skipped' => $skipped,
            ]);
        } catch (Throwable $exception) {
            $run->update([
                'status' => 'failed',
                'finished_at' => now(),
                'error_message' => $exception->getMessage(),
            ]);
        } finally {
            $lock->release();
        }

        return $run->fresh();
    }

    private function screenAttributes(array $display, array $tags): array
    {
        $displayId = (int) Arr::get($display, 'displayId');
        [$municipality, $province] = $this->locationParts(Arr::get($display, 'address'));

        return [
            'public_code' => Arr::get($display, 'publicCode', 'ELIXE-'.str_pad((string) $displayId, 3, '0', STR_PAD_LEFT)),
            'display_name' => (string) Arr::get($display, 'display', Arr::get($display, 'displayName', 'Pantalla '.$displayId)),
            'public_name' => Arr::get($display, 'description') ?: null,
            'description' => Arr::get($display, 'description'),
            'address' => Arr::get($display, 'address'),
            'municipality' => $municipality,
            'province' => $province,
            'latitude' => Arr::get($display, 'latitude'),
            'longitude' => Arr::get($display, 'longitude'),
            'location_type' => $tags['loc_tipo'] ?? null,
            'location_sector' => $tags['loc_sector'] ?? null,
            'web_visible_from_xibo' => filter_var($tags['web_visible'] ?? false, FILTER_VALIDATE_BOOL),
            'commercial_status' => $tags['com_estado'] ?? null,
            'display_type' => Arr::get($display, 'displayType'),
            'orientation' => Arr::get($display, 'orientation'),
            'resolution' => Arr::get($display, 'resolution'),
            'licensed' => (bool) Arr::get($display, 'licensed', false),
            'logged_in' => (bool) Arr::get($display, 'loggedIn', false),
            'media_inventory_status' => (bool) Arr::get($display, 'mediaInventoryStatus', false),
            'last_accessed_at' => $this->dateOrNull(Arr::get($display, 'lastAccessed')),
            'xibo_display_group_id' => Arr::get($display, 'displayGroupId'),
            'raw_xibo_payload' => null,
            'synced_at' => now(),
        ];
    }

    private function locationParts(?string $address): array
    {
        if (! $address) {
            return [null, null];
        }

        preg_match('/\b\d{5}\s+([^,]+),\s*([^,]+)$/u', $address, $matches);

        if ($matches) {
            return [trim($matches[1]), trim($matches[2])];
        }

        $parts = array_values(array_filter(array_map('trim', explode(',', $address))));

        return [
            $parts[count($parts) - 2] ?? null,
            $parts[count($parts) - 1] ?? null,
        ];
    }

    private function dateOrNull(mixed $value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (Throwable) {
            return null;
        }
    }
}
