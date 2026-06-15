<?php

namespace Tests\Unit;

use App\Services\Localization\AdminUiTranslator;
use Illuminate\Http\Response;
use Tests\TestCase;

class AdminUiTranslatorTest extends TestCase
{
    public function test_it_translates_visible_admin_text_without_touching_scripts_or_values(): void
    {
        app()->setLocale('ru');

        $response = new Response('<main><h1>Finance Control</h1><input value="Finance Control" placeholder="Name"><script>const label = "Finance Control";</script></main>', 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);

        $html = app(AdminUiTranslator::class)->translateResponse($response)->getContent();

        $this->assertStringContainsString('Финансовый контроль', $html);
        $this->assertStringContainsString('placeholder="Название"', $html);
        $this->assertStringContainsString('value="Finance Control"', $html);
        $this->assertStringContainsString('const label = "Finance Control";', $html);
    }

    public function test_it_translates_livewire_json_html_fragments(): void
    {
        app()->setLocale('ru');

        $response = new Response(json_encode(['components' => [['effects' => ['html' => '<button>Finance Control</button>']]]]), 200, [
            'Content-Type' => 'application/json',
        ]);

        $payload = json_decode(app(AdminUiTranslator::class)->translateResponse($response)->getContent(), true);

        $this->assertStringContainsString('Финансовый контроль', $payload['components'][0]['effects']['html']);
    }
}
