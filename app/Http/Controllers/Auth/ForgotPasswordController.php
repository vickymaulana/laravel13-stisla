<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\OtpPasswordResetNotification;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     * Handle the password reset link request.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        $response = $this->broker()->sendResetLink(
            $this->credentials($request)
        );

        if ($response === Password::RESET_LINK_SENT) {
            if ($this->usesOtpReset()) {
                $this->sendOtp((string) $request->email);
            }

            return $this->sendResetLinkResponse($request, $response);
        }

        return $this->sendResetLinkFailedResponse($request, $response);
    }

    /**
     * Handle sending OTP for password reset as an additional verification step.
     */
    protected function sendOtp(string $email): void
    {
        $user = User::where('email', $email)->first();

        if (! $user) {
            return;
        }

        $otp = (string) random_int(100000, 999999);
        $expireMinutes = max(1, (int) config('auth.password_reset_otp_expire', 10));
        $cacheKey = $this->otpCacheKey($email);

        Cache::put($cacheKey, Hash::make($otp), now()->addMinutes($expireMinutes));
        Notification::route('mail', $email)->notify(new OtpPasswordResetNotification($otp, $expireMinutes));
    }

    /**
     * Determine whether OTP protection is enabled.
     */
    protected function usesOtpReset(): bool
    {
        return config('auth.password_reset_method', 'token') === 'otp';
    }

    /**
     * Get the cache key for the OTP.
     */
    protected function otpCacheKey(string $email): string
    {
        return 'password-reset:otp:'.sha1(strtolower($email));
    }
}
