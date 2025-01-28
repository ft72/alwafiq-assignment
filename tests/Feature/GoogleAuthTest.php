<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_user_can_authenticate_with_google()
    {
        $abstractUser = Mockery::mock(\Laravel\Socialite\Two\User::class);
        $abstractUser->shouldReceive('getEmail')->andReturn('test@gmail.com');
        $abstractUser->shouldReceive('getName')->andReturn('Test User');
        $abstractUser->shouldReceive('getId')->andReturn('1234567890');

        Socialite::shouldReceive('driver->stateless->user')->andReturn($abstractUser);

        $response = $this->get('/auth/google/callback');

        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'test@gmail.com',
            'name' => 'Test User',
        ]);

        $response->assertRedirect('/dashboard');
    }
}
