<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\ContentBlock;
use App\Models\Faq;
use App\Models\LegalPage;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CmsFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_content_seeder_creates_public_cms_foundation(): void
    {
        $this->seed(ContentSeeder::class);

        $this->assertDatabaseHas('content_blocks', ['key' => 'hero', 'active' => true]);
        $this->assertDatabaseHas('faqs', ['category' => 'locales', 'active' => true]);
        $this->assertDatabaseHas('legal_pages', ['slug' => 'privacidad', 'active' => true]);
        $this->assertDatabaseHas('settings', ['key' => 'leads_email']);

        $this->assertNotNull(ContentBlock::where('key', 'hero')->first()?->publicPayload('gl')['title']);
        $this->assertNotNull(Faq::first()?->publicPayload('gl')['question']);
        $this->assertNotNull(LegalPage::where('slug', 'privacidad')->first()?->publicPayload('gl')['content']);
    }

    public function test_admin_can_update_leads_email_setting_and_it_is_audited(): void
    {
        $admin = User::factory()->create();
        $setting = Setting::create([
            'key' => 'leads_email',
            'value' => 'old@example.test',
            'label' => 'Email receptor de leads',
            'type' => 'email',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.settings.update', $setting), ['value' => 'new@example.test'])
            ->assertRedirect();

        $this->assertSame('new@example.test', $setting->fresh()->value);
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $admin->id,
            'action' => 'settings.leads_email_changed',
            'auditable_type' => Setting::class,
            'auditable_id' => $setting->id,
        ]);

        $this->assertSame(1, AuditLog::count());
    }
}
