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
        User::factory()->create([
            'email' => 'faizantahir6969@gmail.com',
            'name' => 'Faizan Tahir',
            'password' => bcrypt('password'),
        ]);

        $abstractUser = Mockery::mock(\Laravel\Socialite\Two\User::class);
        $abstractUser->shouldReceive('getEmail')->andReturn('faizantahir6969@gmail.com');
        $abstractUser->shouldReceive('getName')->andReturn('Faizan Tahir');
        $abstractUser->shouldReceive('getId')->andReturn('1');

        $mockSocialiteDriver = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $mockSocialiteDriver->shouldReceive('user')->andReturn($abstractUser);

        $mockSocialiteDriver->shouldReceive('setHttpClient')->andReturnSelf();

        Socialite::shouldReceive('driver->setHttpClient')->andReturn($mockSocialiteDriver);

        $response = $this->get('/auth/google/callback');

        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'faizantahir6969@gmail.com',
            'name' => 'Faizan Tahir',
        ]);

        $response->assertRedirect('/dashboard');
    }
}