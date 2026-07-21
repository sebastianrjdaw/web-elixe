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
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CmsFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_content_seeder_creates_public_cms_foundation(): void
    {
        $this->seed(ContentSeeder::class);

        $this->assertDatabaseHas('content_blocks', ['key' => 'hero', 'active' => true]);
        $this->assertDatabaseHas('content_blocks', ['key' => 'feature_screens', 'active' => true]);
        $this->assertDatabaseHas('content_blocks', ['key' => 'process_launch', 'active' => true]);
        $this->assertDatabaseHas('faqs', ['category' => 'locales', 'active' => true]);
        $this->assertDatabaseHas('legal_pages', ['slug' => 'privacidad', 'active' => true]);
        $this->assertDatabaseHas('settings', ['key' => 'leads_email']);

        $this->assertNotNull(ContentBlock::where('key', 'hero')->first()?->publicPayload('gl')['title']);
        $this->assertNotNull(Faq::first()?->publicPayload('gl')['question']);
        $this->assertNotNull(LegalPage::where('slug', 'privacidad')->first()?->publicPayload('gl')['content']);
    }

    public function test_admin_can_edit_a_home_card_and_public_home_uses_the_new_copy(): void
    {
        $this->seed(ContentSeeder::class);
        $admin = User::factory()->create(['is_admin' => true]);
        $card = ContentBlock::where('key', 'feature_screens')->firstOrFail();

        $this->actingAs($admin)->patch(route('admin.content.update', $card), [
            'title_es' => 'Pantallas verificadas', 'title_gl' => 'Pantallas verificadas',
            'subtitle_es' => null, 'subtitle_gl' => null,
            'content_es' => 'Nuevo contenido comercial.', 'content_gl' => 'Novo contido comercial.',
            'active' => true,
        ])->assertRedirect()->assertSessionHas('success');

        $this->assertDatabaseHas('content_blocks', ['key' => 'feature_screens', 'title_es' => 'Pantallas verificadas']);
        $this->assertDatabaseHas('audit_logs', ['action' => 'content.updated', 'auditable_id' => $card->id]);
        $this->get(route('home'))->assertInertia(fn (Assert $page) => $page
            ->component('Home', false)
            ->where('contentBlocks.feature_screens.title', 'Pantallas verificadas')
            ->where('contentBlocks.feature_screens.content', 'Nuevo contenido comercial.'));
    }

    public function test_hidden_home_card_is_not_sent_to_the_public_frontend(): void
    {
        $this->seed(ContentSeeder::class);
        ContentBlock::where('key', 'feature_proximity')->update(['active' => false]);

        $this->get(route('home'))->assertInertia(fn (Assert $page) => $page
            ->component('Home', false)
            ->missing('contentBlocks.feature_proximity'));
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
