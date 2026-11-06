<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\UserService;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Enums\TokenEnum;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Password;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Dedoc\Scramble\Attributes\Group;
use App\Traits\HasImage;

#[Group('Authentication', description: 'Register, log in, manage tokens, and reset passwords.')]
class AuthController extends Controller
{
    use HasImage;
    public function __construct(private UserService $userService){}

    /**
     * Register a new user.
     *
     * Creates an account and sends an email verification notification.
     *
     * @unauthenticated
     */
    public function register(RegisterRequest $request){
        $data = $request->validated();
        $profile_pic = $request->file('profile_pic');
        $data['password'] = Hash::make($data['password']);
        $user = $this->userService->create($data);
        if($profile_pic){
            $image_path = $this->getStoredImagePath($profile_pic, 'users/'.$user->id.'/');
            $user->update(['profile_pic' => $image_path ]);
        }

        event(new Registered($user));
        
        return response()->json([
            'status' => "Success",
            'data' => UserResource::make($user->fresh())
        ], 201);

    }
    /**
     * Log in.
     *
     * Returns access and refresh tokens when credentials are valid.
     *
     * @unauthenticated
     */
    public function login(LoginRequest $request){
        $data = $request->validated();
        $user = $this->userService->findAll(['email'=> $data['email']])->first();
        if(!$user){
            return response()->json([
                'status' => 'Error',
                'message' => 'Credentials are invalid'
            ], 401);
        }
        $check_password = Hash::check($data['password'], $user->password);
        if(!$check_password){
            return response()->json([
                'status' => 'Error',
                'message' => 'Credentials are invalid'
            ], 401);
        }
        $access_token = $user->createToken('access_token',[TokenEnum::ACCESS_API->value],now()->addMinutes((int) (config('sanctum.access_token_expiration'))))->plainTextToken;

        $refresh_token = $user->createToken('refresh_token',[TokenEnum::ISSUE_ACCESS_TOKEN->value],now()->addMinutes((int) (config('sanctum.refresh_token_expiration')) ))->plainTextToken; //24 h * 60 mins = 1 day
         return response()->json([
            'status' => "Success",
            'message' => 'Logged in',
            'access_token' => $access_token,
            'refresh_token' => $refresh_token,
            'user'=> UserResource::make($user)
        ], 200);
    }
    /**
     * Get the current user.
     *
     * Returns the authenticated user together with their posts.
     */
    public function profile(Request $request){
        //get the authenticated user making the request
        $user = $request->user()->load(['posts','likes.post:id']);
        return response()->json([
            'status' => "Success",
            'user'=> UserResource::make($user) 
        ], 200);
    }

  
    /**
     * Refresh the access token.
     *
     * Issues a new access token using a valid refresh token.
     */
    public function refresh(){
        $user = Auth::user(); //get the currently authorized user
       $access_token = $user->createToken('access_token',[TokenEnum::ACCESS_API->value],now()->addMinutes((int) (config('sanctum.access_token_expiration'))))->plainTextToken;

         return response()->json([
            'status' => "Success",
            'message' => 'New access token is retreived',
            'access_token' => $access_token,
        ], 200);
    }
    
    /**
     * Request a password reset.
     *
     * Sends a reset link if an account exists for the given email.
     *
     * @unauthenticated
     */
    public function forgotPassword(ForgotPasswordRequest $request){
        $email = $request->validated('email');
        $status = Password::sendResetLink([
            'email' => $email ,
        ]);


        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'a reset link is sent to your email : '.$email .'.',
            ], 200);
        }

        if ($status === Password::RESET_THROTTLED) {
            return response()->json([
                'message' => 'Please wait before requesting another link.',
            ], 429);
        }

        return response()->json([
            'message' => 'Unable to send the password reset link.',
        ], 400);
    }
    
    /**
     * Reset the password.
     *
     * Sets a new password using a valid reset token.
     *
     * @unauthenticated
     */
    public function resetPassword(ResetPasswordRequest $request){
        $status = $this->userService->resetPassword($request->validated());

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Password reset successfully.',
            ]);
        }

        if ($status === Password::RESET_THROTTLED) {
            return response()->json([
                'message' => 'Please wait before trying again.',
            ], 429);
        }

        return response()->json([
            'message' => 'Invalid link.',
        ], 422);
    }


    /**
     * Log out.
     *
     * Revokes the current access token.
     */
    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json([
                'status' => 'Success',
                'message' => 'loggedOut successfully'
            ], 204);
    }
}
