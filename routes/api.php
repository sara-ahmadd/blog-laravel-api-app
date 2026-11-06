<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Post\PostController;
use App\Http\Controllers\Api\V1\Like\LikeController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Comment\CommentController;
use App\Http\Controllers\Api\V1\Post\PostDraftGenerationController;
use App\Http\Controllers\Api\V1\Auth\EmailVerificationController;
use App\Enums\TokenEnum;


Route::get('/hello', function (Request $request) {
    return ['message' => 'hello'];
});


Route::prefix('v1')->group(
    function(){        
        // ***Email verification***
        Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware(['signed','throttle:6,1'])->name('verification.verify');

        Route::get('/email/resend', [EmailVerificationController::class, 
        'resend'])->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.resend');
        


        // ***Authentication & Authorization***
        Route::prefix('auth')->group(function(){
            Route::post('/register', [AuthController::class, 'register']);

            
            Route::post('/login', [AuthController::class, 'login']);
            
            
            // forgot password
            Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->middleware(['throttle:10,1'])->name('password.forgot');
            
            // Reset Password 
            Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware(['throttle:6,1'])->name('password.reset');
            
            Route::middleware(['auth:sanctum',"abilities:".TokenEnum::ACCESS_API->value])->group( function(){
                Route::get('/profile', [AuthController::class, 'profile']);
                Route::get('/logout', [AuthController::class, 'logout']);
            });
                
            Route::post('/refresh', [AuthController::class, 'refresh'])->middleware(["auth:sanctum","abilities:".TokenEnum::ISSUE_ACCESS_TOKEN->value]);
        }); 


        // ***Posts***
        Route::apiResource('posts', PostController::class)->middleware(["auth:sanctum","abilities:".TokenEnum::ACCESS_API->value]);
        Route::put('/posts/{post}', [PostController::class, 'update']);
        Route::post('posts/destroy_image', [PostController::class, 'destroyImage'])->middleware(["auth:sanctum","abilities:".TokenEnum::ACCESS_API->value]);
        Route::post('posts/create_from_draft', [PostController::class, 'createPostFromDraft'])->middleware(["auth:sanctum","abilities:".TokenEnum::ACCESS_API->value]);
        
        // like/unlike a post
        Route::post('/posts/{post}/like', [LikeController::class, 'store'])->middleware(["auth:sanctum","abilities:".TokenEnum::ACCESS_API->value]);
        Route::post('/posts/{post}/unlike', [LikeController::class, 'destroy'])->middleware(["auth:sanctum","abilities:".TokenEnum::ACCESS_API->value]);
        
        // *** comments ***
        Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->middleware(["auth:sanctum","abilities:".TokenEnum::ACCESS_API->value]);
        // get all comments on a post
        Route::get('/posts/{post}/comments', [CommentController::class, 'index'])->middleware(["auth:sanctum","abilities:".TokenEnum::ACCESS_API->value]);
        // reply to a comment
        Route::post('/posts/{post}/comments/{comment}', [CommentController::class, 'reply'])->middleware(["auth:sanctum","abilities:".TokenEnum::ACCESS_API->value]);
        // update a comment
        Route::patch('/posts/{post}/comments/{comment}', [CommentController::class, 'update'])->middleware(["auth:sanctum","abilities:".TokenEnum::ACCESS_API->value]);
        // delete a comment
        Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->middleware(["auth:sanctum","abilities:".TokenEnum::ACCESS_API->value]);
        
        // Generate Post Draft by AI
       Route::apiResource('postDraft', PostDraftGenerationController::class)->only(['index', 'store'])->middleware(["auth:sanctum","abilities:".TokenEnum::ACCESS_API->value]);
        
    }
);
