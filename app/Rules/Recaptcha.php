<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Recaptcha implements Rule
{
    /**
     * Minimum trust score to accept (0.0 = bot, 1.0 = human).
     * Google recommends starting around 0.5 and tuning from real traffic.
     */
    protected float $scoreThreshold = 0.5;

    protected string $errorMessage = 'Please confirm you are not a robot by completing the reCAPTCHA.';

    public function passes($attribute, $value): bool
    {
        if (blank($value)) {
            $this->errorMessage = 'Please complete the reCAPTCHA before submitting.';

            return false;
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('services.recaptcha.secret_key'),
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);

            $json = $response->json();

            if (! ($json['success'] ?? false)) {
                $this->errorMessage = 'reCAPTCHA verification failed. Please try again.';

                return false;
            }

            $score = $json['score'] ?? 0;

            if ($score < $this->scoreThreshold) {
                $this->errorMessage = 'This submission looks automated and was blocked. If you believe this is a mistake, please contact our office.';

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            // If Google's verification endpoint is unreachable, fail closed
            // (reject the submission) rather than silently letting it through.
            Log::warning('reCAPTCHA verification request failed: '.$e->getMessage());

            $this->errorMessage = 'We could not verify the reCAPTCHA right now. Please try again in a moment.';

            return false;
        }
    }

    public function message(): string
    {
        return $this->errorMessage;
    }
}