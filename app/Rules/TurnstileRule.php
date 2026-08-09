<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class TurnstileRule implements ValidationRule
{
    /**
     * Run the validation rule for Cloudflare Turnstile CAPTCHA.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $secretKey = config('services.turnstile.secret_key');

        // If Turnstile secret key is not set, bypass validation for seamless local dev testing
        if (empty($secretKey)) {
            return;
        }

        if (empty($value)) {
            $fail('Please complete the Cloudflare Turnstile security check.');
            return;
        }

        try {
            $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $secretKey,
                'response' => $value,
                'remoteip' => request()->header('CF-Connecting-IP', request()->ip()),
            ]);

            if (!$response->successful() || !($response->json('success') ?? false)) {
                $fail('Cloudflare Turnstile security verification failed. Please try again.');
            }
        } catch (\Throwable $e) {
            $fail('Unable to verify Cloudflare Turnstile token.');
        }
    }
}
