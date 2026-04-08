<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanRegisterWithAvatar(): void
    {
        Storage::fake('public');

        $response = $this->post(route('registration.store'), [
            'email' => 'new@example.com',
            'login' => 'new-user',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('users', [
            'email' => 'new@example.com',
            'login' => 'new-user',
        ]);

        $user = User::query()->where('email', 'new@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('secret123', $user->password));
        Storage::disk('public')->assertExists($user->avatar);
    }

    public function testUserCanLoginWithValidCredentials(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->post(route('web.login'), [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('main'));
        $this->assertAuthenticatedAs($user);
    }
}
