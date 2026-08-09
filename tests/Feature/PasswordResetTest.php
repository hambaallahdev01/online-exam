<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_page_is_accessible()
    {
        $response = $this->get(route('password.request'));
        $response->assertStatus(200);
    }

    public function test_user_can_request_password_reset_link()
    {
        Mail::fake();

        $school = School::create(['name' => 'Demo', 'email' => 'demo@school.org']);
        $user = User::create([
            'school_id' => $school->id,
            'name' => 'Admin User',
            'email' => 'admin@test.org',
            'role' => 'admin',
            'email_verified_at' => now(),
            'password' => Hash::make('oldpassword'),
        ]);

        $response = $this->post(route('password.email'), [
            'email' => 'admin@test.org',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('password_reset_tokens', ['email' => 'admin@test.org']);
    }

    public function test_user_can_reset_password_with_valid_token()
    {
        $school = School::create(['name' => 'Demo', 'email' => 'demo@school.org']);
        $user = User::create([
            'school_id' => $school->id,
            'name' => 'Admin User',
            'email' => 'admin@test.org',
            'role' => 'admin',
            'email_verified_at' => now(),
            'password' => Hash::make('oldpassword'),
        ]);

        $token = 'valid-reset-token-123';
        DB::table('password_reset_tokens')->insert([
            'email' => 'admin@test.org',
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'admin@test.org',
            'password' => 'newsecretpassword123',
            'password_confirmation' => 'newsecretpassword123',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertTrue(Hash::check('newsecretpassword123', $user->fresh()->password));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => 'admin@test.org']);
    }
}
