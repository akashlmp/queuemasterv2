<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\QueuedbUser;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /**
     * Show the email verification notice.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        // Logic to show the email verification notice
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request, $id, $hash)
    {
        $user = QueuedbUser::findOrFail($id);

        if ($user->hasVerifiedEmail()) {
            return redirect('/login')->with('error', 'Email already verified.');
        }

        if ($user->email_verified_at === null || $user->email_verified_at === '') {
            if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
                return redirect('/login')->with('error', 'Invalid verification link.');
            }
            $user->markEmailAsVerified();
            $user->verify = 1;
            $user->status = 1;
            $user->role = 1;
            $user->save();
            return redirect('/login')->with('success', 'Email verified successfully.');
        }
    }

    /**
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request)
    {
        $user_id = $request->input('user_id');

        // Retrieve the user from the database
        $user = QueuedbUser::find($user_id);

        if (! $user) {
            // Handle the case where the user does not exist
            return abort(404);
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('success', 'Your email is already verified.');
        }

        // Send the email verification notification to the user
        $user->sendEmailVerificationNotification();
        // return back()->with('success', 'Verification email has been resent.');
        return redirect()->route('login')->with('success', 'new user created successfully');
    }
}
