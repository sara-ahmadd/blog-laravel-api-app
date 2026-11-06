<?php

namespace App\Http\Controllers\Api\V1\Like;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\LikeService;
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
use App\Models\Post;

#[Group('Like', description: 'The authenticated user can like and unlike a post')]
class LikeController extends Controller
{
    public function __construct(private LikeService $likeService){}
    /**
     * Like a Post
     * Authenticated user can like a post
     */
    public function store(Request $request, Post $post){
        $user = $request->user();
        $like = $this->likeService->exists(['user_id' => $user->id, 'post_id' => $post->id]);
        if($like){
            return response()->json([
                'status'=>'Error',
                "message"=>'you already liked this post',
            ], 409);
        }
        $this->likeService->create(['user_id' => $user->id, 'post_id' => $post->id]);
        return response()->json([
            'status' => 'Success',
            "message" => 'You liked the post',
        ], 201);

    }
    /**
     * Unlike a Post
     * Authenticated user can unlike a post he already liked
     */
 public function destroy(Request $request, Post $post){
        $user = $request->user();
        $like = $this->likeService->findAll(['user_id' => $user->id, 'post_id' => $post->id])->first();
        if(!$like){
            return response()->json([
                'status'=>'Error',
                "message"=>'you didnot like this post',
            ], 400);
        }
        $like->delete();
        return response()->noContent();
    }
}
