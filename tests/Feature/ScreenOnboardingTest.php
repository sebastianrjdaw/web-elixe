<?php

namespace Tests\Feature;

use App\Models\ScreenOnboardingRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScreenOnboardingTest extends TestCase
{
    use RefreshDatabase;

    private function data(array $overrides = []): array
    {
        return array_merge([
            'internal_code' => 'ELIXE-VAL-001', 'establishment_name' => 'Café Atlántico',
            'contact_name' => 'Ana', 'contact_email' => 'ana@example.test', 'contact_phone' => '+34 600 123 123',
            'address' => 'Rúa do Mar 1', 'municipality' => 'Valdoviño', 'province' => 'A Coruña', 'postal_code' => '15552',
            'latitude' => 43.61, 'longitude' => -8.14, 'location_type' => 'cafeteria', 'location_sector' => 'hosteleria',
            'web_visible' => true, 'commercial_status' => 'disponible', 'has_existing_screen' => true,
            'requires_elixe_screen' => false, 'internet_available' => true,
        ], $overrides);
    }

    public function test_admin_can_create_submit_and_approve_an_onboarding_request(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->post(route('admin.screen-onboarding.store'), $this->data())->assertRedirect();
        $item = ScreenOnboardingRequest::firstOrFail();
        $this->assertSame('borrador', $item->status);

        $this->actingAs($admin)->post(route('admin.screen-onboarding.submit', $item))->assertRedirect();
        $this->assertSame('pendiente_revision', $item->fresh()->status);

        $this->actingAs($admin)->post(route('admin.screen-onboarding.approve', $item))->assertRedirect();
        $this->assertSame('aprobado', $item->fresh()->status);
        $this->assertDatabaseHas('audit_logs', ['action' => 'screen_onboarding.approved', 'auditable_id' => $item->id]);
    }

    public function test_incomplete_draft_cannot_be_submitted(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->post(route('admin.screen-onboarding.store'), $this->data(['address' => null, 'longitude' => null]));
        $item = ScreenOnboardingRequest::firstOrFail();

        $this->actingAs($admin)->post(route('admin.screen-onboarding.submit', $item))->assertSessionHasErrors('onboarding');
        $this->assertSame('borrador', $item->fresh()->status);
    }

    public function test_internal_code_is_unique_and_non_admin_is_forbidden(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->post(route('admin.screen-onboarding.store'), $this->data());
        $this->actingAs($admin)->post(route('admin.screen-onboarding.store'), $this->data())->assertSessionHasErrors('internal_code');

        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user)->get(route('admin.screen-onboarding.index'))->assertForbidden();
    }

    public function test_approved_request_cannot_be_edited_or_submitted_again(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $item = new ScreenOnboardingRequest($this->data());
        $item->created_by = $admin->id;
        $item->status = 'aprobado';
        $item->save();

        $this->actingAs($admin)->patch(route('admin.screen-onboarding.update', $item), $this->data(['establishment_name' => 'Cambio']))->assertStatus(409);
        $this->actingAs($admin)->post(route('admin.screen-onboarding.submit', $item))->assertStatus(409);
        $this->assertSame('Café Atlántico', $item->fresh()->establishment_name);
    }
}
