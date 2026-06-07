<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_log_out(): void
    {
        /* ARRANGE */
        $user = User::factory()->create();
        $this->actingAs($user);

        /* ACT */
        $response = $this->post('/logout');

        /* ASSERT */
        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_guest_cannot_log_out(): void
    {
        /* ACT */
        $response = $this->post('/logout');

        /* ASSERT */
        $this->assertGuest();
        $response->assertRedirect('/login');
    }
}
