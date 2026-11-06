<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\UserRepoInterface;
use App\Repositories\Eloquent\UserRepo;
use App\Repositories\Contracts\PostRepoInterface;
use App\Repositories\Eloquent\PostRepo;
use App\Repositories\Contracts\PostDraftRepoInterface;
use App\Repositories\Eloquent\PostDraftRepo;
use App\Repositories\Contracts\ImageRepoInterface;
use App\Repositories\Eloquent\ImageRepo;
use App\Repositories\Contracts\LikeRepoInterface;
use App\Repositories\Eloquent\LikeRepo;
use App\Repositories\Contracts\CommentRepoInterface;
use App\Repositories\Eloquent\CommentRepo;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            UserRepoInterface::class,
            UserRepo::class
        );
        $this->app->bind(
            PostRepoInterface::class,
            PostRepo::class
        );
        $this->app->bind(
            PostDraftRepoInterface::class,
            PostDraftRepo::class
        );
        $this->app->bind(
            ImageRepoInterface::class,
            ImageRepo::class
        );
        $this->app->bind(
            LikeRepoInterface::class,
            LikeRepo::class
        );
        $this->app->bind(
            CommentRepoInterface::class,
            CommentRepo::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
       RateLimiter::for('api', function(Request $request){
        return Limit::perMinute(20)->by($request->user()?->id ?: $request->ip);
       });
       RateLimiter::for('global', function(Request $request){
        return Limit::perMinute(120)->by($request->user()?->id ?: $request->ip);
       });
       RateLimiter::for('uploads', function(Request $request){
        return $request->user()->vipCustomer()?
            Limit::none():
            Limit::perMinute(10)->by($request->ip);
       });

       Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer')
                );
            });
    }
}
