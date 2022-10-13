<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\ForgotPasswordNotification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    /**
     * @throws ValidationException
     */
    public function login(Request $request): array
    {
        $user = User::where('email', $request->email)->first();
        /* @var $user User */

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $this->loginResponse($user);
    }

    private function loginResponse(User $user): array
    {
        return [
            'token' => $user->createToken($user->email)->plainTextToken,
            'user' => $user,
        ];
    }

    public function register(Request $request): array
    {
        $user = new User();

        $request->validate($user->getValidationRules(['email', 'password', 'name']));

        $user = $user->fill([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->save();
        $user->sendEmailVerificationNotification();

        return $this->loginResponse($user);
    }

    public function forgotPassword(Request $request): string
    {
        $user = User::where('email', $request->email)->first();
        /* @var $user User */

        if (!$user) {
            abort(400, 'This email is not registered.');
        }

        $user->generateOtp();
        $user->notify(new ForgotPasswordNotification());

        return 'Email sent';
    }

    public function resetPassword(Request $request): string
    {
        $user = User::where('email', $request->email)->first();
        /* @var $user User */

        if (!$user) {
            abort(400, 'This email is not registered.');
        }

        if ($user->otp !== $request->otp) {
            abort(400, 'Invalid OTP.');
        }

        $request->validate($user->getValidationRules('password'));

        $user->otp = '';
        $user->password = Hash::make($request->password);
        $user->save();

        return 'Password has been reset';
    }

    public function changePassword(Request $request): string
    {
        $user = User::current();

        if (!Hash::check($request->password, $user->password)) {
            abort(400, 'Invalid password.');
        }

        $request->validate($user->getValidationRules('password'));

        $user->password = Hash::make($request->new_password);
        $user->save();

        return 'Password has been changed';
    }

    public function update(): string
    {
        $user = User::current();

        request()->validate($user->getValidationRules(['email', 'name']));

        $user->email = \request('email');
        $user->name = \request('name');
        $user->company = \request('company');
        $user->save();

        return '';
    }

    public function logout(Request $request): Response
    {
        $request->user()->currentAccessToken()->delete();
        return response('Logged out');
    }
}
