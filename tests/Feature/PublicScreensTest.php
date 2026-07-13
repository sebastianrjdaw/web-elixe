<?php

namespace Tests\Feature;

use App\Models\Screen;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicScreensTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_map_endpoint_only_returns_safe_screen_fields(): void
    {
        Screen::create([
            'xibo_display_id' => 4,
            'public_code' => 'ELIXE-004',
            'display_name' => 'ELIXE-004',
            'public_name' => 'Casa Marino',
            'address' => 'C-646, 46, 15550 Valdovino, A Coruna',
            'municipality' => 'Valdovino',
            'province' => 'A Coruna',
            'latitude' => 43.5832513,
            'longitude' => -8.1820912,
            'location_type' => 'bar',
            'location_sector' => 'hosteleria',
            'web_visible_from_xibo' => true,
            'commercial_status' => 'disponible',
            'logged_in' => true,
            'licensed' => true,
            'media_inventory_status' => true,
            'synced_at' => now(),
        ]);

        $response = $this->getJson('/api/public/screens/map')
            ->assertOk()
            ->assertJsonPath('0.name', 'Casa Marino')
            ->assertJsonPath('0.municipality', 'Valdovino')
            ->assertJsonPath('0.commercialStatus', 'disponible');

        $payload = $response->json('0');

        $this->assertArrayNotHasKey('xibo_display_id', $payload);
        $this->assertArrayNotHasKey('publicCode', $payload);
        $this->assertArrayNotHasKey('address', $payload);
        $this->assertArrayNotHasKey('display_name', $payload);
    }
}
