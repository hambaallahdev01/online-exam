<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_resend_verification_page_is_accessible()
    {
        $response = $this->get(route('verification.resend.show'));
        $response->assertStatus(200);
    }

    public function test_unverified_user_can_request_resend_verification_email()
    {
        Mail::fake();

        $school = School::create(['name' => 'Demo', 'email' => 'demo@school.org']);
        $user = User::create([
            'school_id' => $school->id,
            'name' => 'Unverified User',
            'email' => 'unverified@test.org',
            'role' => 'admin',
            'email_verified_at' => null,
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
        ]);

        $response = $this->post(route('verification.resend'), [
            'email' => 'unverified@test.org',
        ]);

        $response->assertSessionHas('success');
    }

    public function test_verified_user_requesting_resend_is_informed()
    {
        $school = School::create(['name' => 'Demo', 'email' => 'demo@school.org']);
        $user = User::create([
            'school_id' => $school->id,
            'name' => 'Verified User',
            'email' => 'verified@test.org',
            'role' => 'admin',
            'email_verified_at' => now(),
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
        ]);

        $response = $this->post(route('verification.resend'), [
            'email' => 'verified@test.org',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('info');
    }

    public function test_clicking_valid_verification_link_verifies_email()
    {
        $school = School::create(['name' => 'Demo', 'email' => 'demo@school.org']);
        $user = User::create([
            'school_id' => $school->id,
            'name' => 'User',
            'email' => 'user@test.org',
            'role' => 'admin',
            'email_verified_at' => null,
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
        ]);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        $response = $this->get($url);

        $response->assertRedirect(route('login'));
        $this->assertNotNull($user->fresh()->email_verified_at);
    }
}
