<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;

class ResetPasswordController extends Controller
{
    public function showResetPasswordForm(Request $request, $token)
    {
        return view('auth-profile.reset-password')->with(['token' => $token, 'email' => $request->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|confirmed|min:8|regex:/^(?=.*[a-zA-Z0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]+$/',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                ])->save();
            }
        );

        Session::flash('success', '<i class="fa fa-check-circle"></i> Your password has been updated successfully!');

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with(['success' => __($status)])
            : back()->withErrors(['email' => [__($status)]]);
    }
}
