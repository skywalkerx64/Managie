<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmailVerificationController extends Controller
{
    public function verify($user_id, Request $request) {
        if (!$request->hasValidSignature()) {
            return redirect()->to(env('FRONTEND_URL', 'http://localhost:8080').'/'.trans('email.invalid'));
        }
    
        $user = User::findOrFail($user_id);
    
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            $user->update(['can_login' => true]);
        }
        return redirect()->to(env('FRONTEND_URL', 'http://localhost:8080').'/login');
    }
    
    public function resend() {

        $user = User::findOrFail(auth()->user()->id);
        if ($user->hasVerifiedEmail()) {
            return response()->json(["msg" => trans('email.already_verified')], 400);
        }
    
        $user->sendEmailVerificationNotification();
    
        return response()->json(["msg" => trans('email.mail_sent')]);
    }
}
