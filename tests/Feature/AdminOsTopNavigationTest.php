<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AdminOsTopNavigationTest extends TestCase
{
    public function test_admin_os_top_navigation_renders_on_core_pages(): void
    {
        config(['app.env' => 'local']);

        $user = new User([
            'name' => 'BiKuBe Admin OS Test User',
            'email' => 'admin-os-test@bikube.test',
        ]);

        $user->id = 1;
        $user->exists = true;

        $expectedLinks = [
            '/admin',
            '/admin/operations-command-center',
            '/admin/dispatch-center',
            '/admin/orders-hub',
            '/admin/people-workforce',
            '/admin/services-catalog',
            '/admin/finance-control',
            '/admin/support-center',
            '/admin/content-cms',
            '/admin/system-security',
        ];

        foreach ($expectedLinks as $path) {
            $response = $this->actingAs($user)->get($path);

            $response->assertOk();
            $response->assertSee('bkb-top-module-nav', false);
            $response->assertSee('BiKuBe Admin OS module switcher', false);

            $html = $response->getContent();

            $this->assertSame(1, substr_count($html, 'aria-current="page"'), "Expected exactly one active top navigation item on [{$path}].");

            foreach ($expectedLinks as $link) {
                $response->assertSee('href="' . $link . '"', false);
            }
        }
    }
}