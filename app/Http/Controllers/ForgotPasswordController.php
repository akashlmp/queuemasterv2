<?php

namespace App\Http\Controllers;

use App\Models\QueuedbUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;

class ForgotPasswordController extends Controller
{
    public function showForgotPasswordForm()
    {
        return view('auth-profile.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = QueuedbUser::where('email', $request->email)->first();
        if (! $user) {
            Session::flash('error', '<i class="fa fa-exclamation-circle"></i> Email does not exist.');
            return back();
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            Session::flash('success', '<i class="fa fa-check-circle"></i> We have emailed your password reset link.');
        } else {
            Session::flash('error', '<i class="fa fa-exclamation-circle"></i> Forgot password link not sent on your email');
        }

        return back();
    }
}
