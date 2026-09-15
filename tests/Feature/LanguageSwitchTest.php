<?php

namespace Tests\Feature;

use Tests\TestCase;

class LanguageSwitchTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_user_can_switch_language(): void
    {
        $response = $this->from('/login')->get('/language/en');

        $response->assertRedirect('/login');
        $this->assertSame('en', session('locale'));
        $this->assertSame('en', app()->getLocale());
    }
}
