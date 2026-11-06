<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;

#[Group('Email verification', description: 'Confirm a user email address from a signed link.')]
class EmailVerificationController extends Controller
{
    /**
     * Verify an email address.
     *
     * Confirms the user's email using a signed verification link.
     *
     * @unauthenticated
     */
    public function verify(Request $request, int $id, string $hash)
    {
        $user = User::findOrFail($id);

        if (! hash_equals(
            sha1($user->getEmailForVerification()),
            $hash
        )) {
            return response()->json([
                'message' => 'Invalid verification link.'
            ], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified.'
            ]);
        }

        $user->markEmailAsVerified();

        return response()->json([
            'message' => 'Email verified successfully.'
        ]);
    }

    /**
     * Resend the verification email.
     *
     * Sends a new verification email to the authenticated user if the address is still unverified.
     */
    public function resend(Request $request){
        if($request->user()->hasVerifiedEmail()){
            return response()->json([
                'message'=>'Email is already verified'
            ], 400);
        }

        $request->user()->sendEmailVerificationNotification();
        return response()->json([
                'message'=>'Verification Email is sent'
         ], 200);
    }
}
