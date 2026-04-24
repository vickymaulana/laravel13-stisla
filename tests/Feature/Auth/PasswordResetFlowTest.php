<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        $this->setResetMethod('token');
        parent::tearDown();
    }

    public function test_password_can_be_reset_with_standard_token_flow(): void
    {
        $this->setResetMethod('token');

        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $response->assertRedirect('/home');
        $this->assertTrue(Hash::check('new-password-123', $user->fresh()->password));
    }

    public function test_password_reset_with_otp_requires_valid_code(): void
    {
        $this->setResetMethod('otp');

        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);
        Cache::put('password-reset:otp:'.sha1(strtolower($user->email)), Hash::make('123456'), now()->addMinutes(10));

        $invalidOtp = $this->from(route('password.reset', ['token' => $token, 'email' => $user->email]))
            ->post(route('password.update'), [
                'token' => $token,
                'email' => $user->email,
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
                'otp' => '111111',
            ]);

        $invalidOtp->assertRedirect(route('password.reset', ['token' => $token, 'email' => $user->email]));
        $invalidOtp->assertSessionHasErrors('otp');

        $validOtp = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
            'otp' => '123456',
        ]);

        $validOtp->assertRedirect('/home');
        $this->assertTrue(Hash::check('new-password-123', $user->fresh()->password));
    }

    private function setResetMethod(string $method): void
    {
        config(['auth.password_reset_method' => $method]);
    }
}
