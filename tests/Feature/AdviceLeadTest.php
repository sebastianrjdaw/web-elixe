<?php

namespace Tests\Feature;

use App\Mail\LeadConfirmation;
use App\Mail\NewLeadNotification;
use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdviceLeadTest extends TestCase
{
    use RefreshDatabase;

    public function test_advice_form_creates_venue_lead_when_privacy_is_accepted(): void
    {
        Mail::fake();
        config(['services.turnstile.enabled' => false]);

        $response = $this->post(route('advice.store'), $this->validVenuePayload());

        $response->assertRedirect(route('thanks'));

        $lead = Lead::query()->firstOrFail();

        $this->assertSame('venue', $lead->type);
        $this->assertSame('Cafe Norte', $lead->business_name);
        $this->assertSame('Ana Garcia', $lead->contact_name);
        $this->assertSame('678123456', $lead->phone);
        $this->assertNotNull($lead->privacy_accepted_at);
        $this->assertNull($lead->captcha_verified_at);
    }

    public function test_advice_form_rejects_invalid_spanish_phone(): void
    {
        Mail::fake();
        config(['services.turnstile.enabled' => false]);

        $response = $this
            ->from(route('advice'))
            ->post(route('advice.store'), $this->validVenuePayload([
                'phone' => '512345678',
            ]));

        $response
            ->assertRedirect(route('advice'))
            ->assertSessionHasErrors('phone');

        $this->assertDatabaseCount('leads', 0);
        Mail::assertNotQueued(NewLeadNotification::class);
        Mail::assertNotQueued(LeadConfirmation::class);
    }

    public function test_advice_form_queues_internal_notification_and_user_confirmation(): void
    {
        Mail::fake();
        config([
            'services.elixe.leads_email' => 'leads@example.test',
            'services.turnstile.enabled' => false,
        ]);

        $this->post(route('advice.store'), $this->validVenuePayload([
            'email' => 'ana@example.test',
        ]))->assertRedirect(route('thanks'));

        $lead = Lead::query()->firstOrFail();

        Mail::assertQueued(NewLeadNotification::class, function (NewLeadNotification $mail) use ($lead) {
            return $mail->hasTo('leads@example.test')
                && $mail->lead->is($lead);
        });

        Mail::assertQueued(LeadConfirmation::class, function (LeadConfirmation $mail) use ($lead) {
            return $mail->hasTo('ana@example.test')
                && $mail->lead->is($lead);
        });
    }

    private function validVenuePayload(array $overrides = []): array
    {
        return array_merge([
            'type' => 'venue',
            'business_name' => 'Cafe Norte',
            'contact_name' => 'Ana Garcia',
            'email' => 'ana@example.test',
            'phone' => '678123456',
            'province' => 'A Coruna',
            'municipality' => 'Valdovino',
            'location_type' => 'bar',
            'has_screen' => '1',
            'wants_elixe_screen' => '1',
            'wants_ad_control' => '0',
            'preferred_contact_method' => 'llamada',
            'preferred_call_time' => 'manana',
            'message' => 'Quiero valorar una pantalla en el local.',
            'privacy_accepted' => '1',
        ], $overrides);
    }
}
