<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Recaptcha implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (blank($value)) {
            $fail('Please complete the reCAPTCHA before submitting.');
            return;
        }

        $secret = config('services.recaptcha.secret_key');
        if (blank($secret)) {
            Log::error('reCAPTCHA secret key is not configured.');
            $fail('reCAPTCHA is temporarily unavailable. Please try again later.');
            return;
        }

        try {
            $http = Http::asForm()
                ->timeout((int) config('services.recaptcha.timeout', 10));

            $caBundle = config('services.recaptcha.ca_bundle');
            if (filled($caBundle) && is_file($caBundle)) {
                $http = $http->withOptions(['verify' => $caBundle]);
            }

            $response = $http->post((string) config('services.recaptcha.verify_url', 'https://www.google.com/recaptcha/api/siteverify'), [
                    'secret' => $secret,
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]);

            if (! $response->successful()) {
                Log::warning('reCAPTCHA verification returned HTTP '.$response->status());
                $fail('We could not verify the reCAPTCHA right now. Please try again in a moment.');
                return;
            }

            $json = $response->json();
            if (! ($json['success'] ?? false)) {
                Log::warning('reCAPTCHA verification failed.', ['error_codes' => $json['error-codes'] ?? []]);
                $fail('reCAPTCHA verification failed. Please try again.');
                return;
            }

            $expectedHostname = config('services.recaptcha.expected_hostname');
            if (filled($expectedHostname) && isset($json['hostname']) && $json['hostname'] !== $expectedHostname) {
                Log::warning('reCAPTCHA hostname mismatch.', ['hostname' => $json['hostname']]);
                $fail('reCAPTCHA verification failed. Please use this form on the configured website.');
                return;
            }

            if (config('services.recaptcha.mode') === 'v2') {
                Log::info('reCAPTCHA v2 verification passed.', [
                    'hostname' => $json['hostname'] ?? null,
                    'ip' => request()->ip(),
                ]);
                return;
            }

            if (array_key_exists('score', $json)) {
                $threshold = (float) config('services.recaptcha.score_threshold', 0.5);
                $score = (float) $json['score'];
                if ($score < $threshold) {
                    Log::warning('reCAPTCHA v3 score rejected.', ['score' => $score, 'threshold' => $threshold, 'ip' => request()->ip()]);
                    $fail('This submission looks automated and was blocked. If you believe this is a mistake, please contact our office.');
                    return;
                }

                $action = $json['action'] ?? null;
                if ($action !== null && ! in_array($action, ['login', 'register'], true)) {
                    Log::warning('reCAPTCHA action mismatch.', ['action' => $action]);
                    $fail('reCAPTCHA verification failed. Please refresh the page and try again.');
                    return;
                }

                Log::info('reCAPTCHA v3 verification passed.', [
                    'score' => $score,
                    'threshold' => $threshold,
                    'action' => $action,
                    'hostname' => $json['hostname'] ?? null,
                    'ip' => request()->ip(),
                ]);
            } else {
                Log::info('reCAPTCHA verification passed without a v3 score.', [
                    'hostname' => $json['hostname'] ?? null,
                    'ip' => request()->ip(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('reCAPTCHA verification request failed: '.$e->getMessage());
            $fail('We could not verify the reCAPTCHA right now. Please try again in a moment.');
        }
    }
}
