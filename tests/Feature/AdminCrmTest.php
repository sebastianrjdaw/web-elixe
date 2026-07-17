<?php

namespace Tests\Feature;

use App\Mail\DailyLeadSummary;
use App\Mail\LeadResponse;
use App\Models\Lead;
use App\Models\ResponseTemplate;
use App\Models\Screen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminCrmTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_change_lead_status_and_it_is_audited(): void
    {
        $admin = User::factory()->create();
        $lead = Lead::create([
            'type' => 'venue',
            'status' => 'nuevo',
            'business_name' => 'Cafe Norte',
            'contact_name' => 'Ana Garcia',
            'email' => 'ana@example.test',
            'phone' => '678123456',
            'privacy_accepted_at' => now(),
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.leads.status', $lead), ['status' => 'contactado'])
            ->assertRedirect();

        $this->assertSame('contactado', $lead->fresh()->status);
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $admin->id,
            'action' => 'lead.status_changed',
            'auditable_type' => Lead::class,
            'auditable_id' => $lead->id,
        ]);
    }

    public function test_admin_local_override_hides_public_screen_and_is_audited(): void
    {
        $admin = User::factory()->create();
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
            'web_visible_from_xibo' => true,
            'commercial_status' => 'disponible',
            'logged_in' => true,
            'licensed' => true,
            'media_inventory_status' => true,
            'synced_at' => now(),
        ]);

        $this->assertSame(1, Screen::publiclyVisible()->count());

        $this->actingAs($admin)
            ->patch(route('admin.screens.hide', $screen))
            ->assertRedirect();

        $this->assertSame(0, Screen::publiclyVisible()->count());
        $this->assertFalse($screen->fresh()->local_visibility_override);
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $admin->id,
            'action' => 'screen.hidden',
            'auditable_type' => Screen::class,
            'auditable_id' => $screen->id,
        ]);
    }

    public function test_admin_can_send_a_template_response_and_activity_is_recorded(): void
    {
        Mail::fake();
        $admin = User::factory()->create();
        $lead = Lead::create([
            'type' => 'venue',
            'status' => 'nuevo',
            'locale' => 'es',
            'business_name' => 'Cafe Norte',
            'contact_name' => 'Ana Garcia',
            'email' => 'ana@example.test',
            'privacy_accepted_at' => now(),
        ]);
        $template = ResponseTemplate::create([
            'key' => 'followup_test',
            'name' => 'Seguimiento',
            'locale' => 'es',
            'subject' => 'Hola {{contact_name}}',
            'body' => 'Hablemos de {{business_name}}.',
            'is_active' => true,
        ]);

        $rendered = (new LeadResponse($lead, $template))->render();
        $this->assertStringContainsString('Hola Ana Garcia', $rendered);
        $this->assertStringContainsString('Hablemos de Cafe Norte.', $rendered);
        $this->assertStringNotContainsString('{{contact_name}}', $rendered);

        $this->actingAs($admin)
            ->post(route('admin.leads.response', $lead), ['response_template_id' => $template->id])
            ->assertRedirect()
            ->assertSessionHas('success');

        Mail::assertQueued(LeadResponse::class, fn (LeadResponse $mail) => $mail->hasTo($lead->email));
        $this->assertDatabaseHas('lead_activities', [
            'lead_id' => $lead->id,
            'user_id' => $admin->id,
            'action' => 'response_sent',
        ]);
    }

    public function test_daily_summary_is_queued_with_yesterdays_leads(): void
    {
        Mail::fake();
        $lead = Lead::create([
            'type' => 'venue',
            'status' => 'nuevo',
            'business_name' => 'Cafe Norte',
            'contact_name' => 'Ana Garcia',
            'email' => 'ana@example.test',
            'privacy_accepted_at' => now(),
        ]);
        $lead->forceFill(['created_at' => now()->subDay()->setTime(12, 0)])->save();

        $this->artisan('leads:send-daily-summary')->assertSuccessful();

        Mail::assertQueued(DailyLeadSummary::class, fn (DailyLeadSummary $mail) => $mail->leads->contains($lead));
    }
}
