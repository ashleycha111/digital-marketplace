<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function a_user_can_logout()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $this->post('/logout');

        $this->assertFalse(Auth::check());
    }

    /**
     * @test
     */
    public function a_user_is_redirected_to_home_page_after_logout()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $this->post('/logout')
            ->assertRedirect('/');
    }
}
