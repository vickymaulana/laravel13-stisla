<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Handle a reset request with optional OTP validation.
     */
    public function reset(Request $request)
    {
        $request->validate($this->rules(), $this->validationErrorMessages());

        if ($this->usesOtpReset() && ! $this->validateOtp($request)) {
            return back()
                ->withErrors(['otp' => 'The provided OTP is invalid or expired.'])
                ->withInput($request->only('email'));
        }

        $response = $this->broker()->reset(
            $this->credentials($request),
            function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        if ($response === Password::PASSWORD_RESET) {
            $this->clearOtpState($request->email);
        }

        return $response === Password::PASSWORD_RESET
            ? $this->sendResetResponse($request, $response)
            : $this->sendResetFailedResponse($request, $response);
    }

    /**
     * Get the password reset validation rules.
     */
    protected function rules(): array
    {
        return [
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
            'otp' => [$this->usesOtpReset() ? 'required' : 'nullable', 'digits:6'],
        ];
    }

    /**
     * Validate password reset credentials.
     */
    protected function credentials(Request $request): array
    {
        return $request->only('email', 'password', 'password_confirmation', 'token');
    }

    /**
     * Reset the given user's password.
     */
    protected function resetPassword($user, $password): void
    {
        $user->forceFill([
            'password' => Hash::make($password),
            'remember_token' => Str::random(60),
        ])->save();

        event(new PasswordReset($user));

        $this->guard()->login($user);
    }

    /**
     * Determine whether OTP protection is enabled.
     */
    protected function usesOtpReset(): bool
    {
        return config('auth.password_reset_method', 'token') === 'otp';
    }

    /**
     * Validate OTP with rate-limiting and expiry checks.
     */
    protected function validateOtp(Request $request): bool
    {
        $email = strtolower((string) $request->email);
        $attemptKey = $this->otpAttemptKey($email, $request->ip());
        $maxAttempts = max(1, (int) config('auth.password_reset_otp_max_attempts', 5));

        if (RateLimiter::tooManyAttempts($attemptKey, $maxAttempts)) {
            return false;
        }

        $hashedOtp = Cache::get($this->otpCacheKey($email));

        if (! $hashedOtp || ! Hash::check((string) $request->otp, $hashedOtp)) {
            RateLimiter::hit($attemptKey, 60);

            return false;
        }

        RateLimiter::clear($attemptKey);

        return true;
    }

    /**
     * Clear OTP state after successful password reset.
     */
    protected function clearOtpState(string $email): void
    {
        $normalizedEmail = strtolower($email);
        Cache::forget($this->otpCacheKey($normalizedEmail));
        RateLimiter::clear($this->otpAttemptKey($normalizedEmail));
    }

    /**
     * Get the cache key for the OTP.
     */
    protected function otpCacheKey(string $email): string
    {
        return 'password-reset:otp:'.sha1($email);
    }

    /**
     * Get the rate limiter key for OTP attempts.
     */
    protected function otpAttemptKey(string $email, ?string $ip = null): string
    {
        return 'password-reset:otp-attempt:'.sha1($email.'|'.($ip ?? 'unknown'));
    }
}
