<?php

namespace App\Services\Xibo;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class XiboService
{
    public function about(): array
    {
        return $this->get('/about');
    }

    public function clock(): array
    {
        return $this->get('/clock');
    }

    public function displays(int $start = 0, int $length = 100): array
    {
        return $this->get('/display', compact('start', 'length'));
    }

    public function allDisplays(int $pageSize = 100): array
    {
        $rows = [];

        for ($start = 0, $page = 0; $page < 100; $start += $pageSize, $page++) {
            $payload = $this->displays($start, $pageSize);
            $batch = array_is_list($payload) ? $payload : ($payload['data'] ?? $payload['rows'] ?? []);
            $rows = array_merge($rows, $batch);

            if (count($batch) < $pageSize) {
                return $rows;
            }
        }

        throw new RuntimeException('Xibo display pagination exceeded the safety limit.');
    }

    public function tags(int $start = 0, int $length = 100): array
    {
        return $this->get('/tag', compact('start', 'length'));
    }

    public function displayGroups(int $start = 0, int $length = 100): array
    {
        return $this->get('/displaygroup', compact('start', 'length'));
    }

    public function accessToken(): string
    {
        $cached = Cache::get('xibo.access_token');

        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        $response = Http::asForm()
            ->retry(2, 200, throw: false)
            ->timeout($this->timeout())
            ->post($this->baseUrl().'/authorize/access_token', [
                'grant_type' => 'client_credentials',
                'client_id' => config('services.xibo.client_id'),
                'client_secret' => config('services.xibo.client_secret'),
            ]);

        if ($response->failed()) {
            throw new RuntimeException('Xibo authorization failed with status '.$response->status().'.');
        }

        $payload = $response->json();

        if (! isset($payload['access_token'])) {
            throw new RuntimeException('Xibo authorization did not return an access token.');
        }

        $ttl = max(((int) ($payload['expires_in'] ?? 3600)) - 60, 60);
        Cache::put('xibo.access_token', $payload['access_token'], now()->addSeconds($ttl));

        return $payload['access_token'];
    }

    public function normalizeTags(array $display): array
    {
        $tags = [];

        foreach (($display['tags'] ?? []) as $tag) {
            if (is_string($tag)) {
                [$name, $value] = array_pad(explode('|', $tag, 2), 2, 'true');
                $tags[trim($name)] = trim($value);

                continue;
            }

            if (is_array($tag)) {
                $name = $tag['tag'] ?? $tag['name'] ?? null;
                if ($name) {
                    $tags[trim((string) $name)] = trim((string) ($tag['value'] ?? 'true'));
                }
            }
        }

        foreach (['loc_tipo', 'loc_sector', 'web_visible', 'com_estado'] as $field) {
            if (isset($display[$field])) {
                $tags[$field] = trim((string) $display[$field]);
            }
        }

        return $tags;
    }

    private function get(string $path, array $query = []): array
    {
        $response = $this->client()
            ->retry(2, 200, throw: false)
            ->get($this->baseUrl().$path, $query);

        if ($response->failed()) {
            throw new RuntimeException("Xibo request failed for {$path} with status {$response->status()}.");
        }

        return $response->json() ?? [];
    }

    private function client(): PendingRequest
    {
        return Http::acceptJson()
            ->withToken($this->accessToken())
            ->timeout($this->timeout());
    }

    private function baseUrl(): string
    {
        return rtrim((string) config('services.xibo.base_url'), '/');
    }

    private function timeout(): int
    {
        return (int) config('services.xibo.timeout', 20);
    }
}
