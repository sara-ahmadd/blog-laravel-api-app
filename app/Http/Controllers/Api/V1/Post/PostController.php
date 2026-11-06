<?php

namespace App\Http\Controllers\Api\V1\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Models\Post;
use App\Traits\HasImage;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PostResource;
use App\Http\Requests\Post\CreatePostFromDraftRequest;
use App\Http\Requests\Post\DeletePostImage;
use App\Services\PostService;
use App\Services\PostDraftService;
use App\Services\ImageService;
use Dedoc\Scramble\Attributes\Group;

use Illuminate\Http\UploadedFile;


#[Group('Posts', description: 'Create, list, update, and delete posts owned by the authenticated user.')]
class PostController extends Controller
{
    use HasImage;
    public function __construct(private PostService $postService, private PostDraftService $postDraftService, private ImageService $imageService){}
    /**
     * List posts.
     *
     * Returns all posts with their authors.
     */
    public function index()
    {
        // add Eager-Loading for author
        $posts = $this->postService->findAll([], ['author','images','likes.user'])->loadCount('likes');
        /*
          return response()->json([
            'message' => 'success',
            'data' => PostResource::collection($posts)
            ], 200);
        */
        return (PostResource::collection($posts))
        ->additional([
            "message" => "Success"
        ])
        ->response()
        ->header('Content-Type', 'application/json')
        ->setStatusCode(200);
    }

    /**
     * Create a post.
     *
     * Creates a post owned by the authenticated user.
     */
    public function store(StorePostRequest $request)
    {
        $curr_user_id = Auth::id();
        
        // Validated text fields
        $data = $request->validated();

        // Validated uploaded files
        $images = $request->file('images', []);

        $post = $this->postService->create([
            'title' => $data['title'],
            'body' => $data['body'],
            'author_id' => $curr_user_id,
        ]);
        $images_paths = array_map(function($img) use ($post) { 
            if(!$img){
                    return response()->json([
                    'status'=>'Error',
                    "message"=>'image is not found',
                     ], 400);
            }
            $path = $this->getStoredImagePath($img,'posts/'.$post->id.'/images');
            $this->imageService->create(['post_id' => $post->id, 'image_path' => $path]);
            return $path;
        }, $images);

        return response()->json([
            'status' => 'Success',
            "message" => 'post is created',
            'data' => PostResource::make($post->load('images'))
        ]);
    }

    /**
     * Get a post.
     *
     * Returns a post if it belongs to the authenticated user.
     */
    public function show(int $id)
    {
        $curr_user_id = Auth::id();
        $post = $this->postService->findById($id)->load('likes.user')->loadCount('likes');
        if(!$post){
            return response()->json([
                'status'=>'Error',
                "message"=>'post is not found',
            ], 400);
        }
        if($post->author->id !== $curr_user_id){
            return response()->json([
                'status'=>'Error',
                "message"=>'post doesnot belong to this user',
            ], 400);
        }
        return response()->json([
            'status'=>'Success',
            "message"=>'post is retrieved',
            'post'=>PostResource::make($post->load('images'))
        ]);
    }

    /**
     * Update a post.
     *
     * Updates the title and body of a post owned by the authenticated user.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $data = $request->validated();
         $curr_user_id = Auth::id();
        if($post->author->id !== $curr_user_id){
             return response()->json([
                'status'=>'Error',
                "message"=>'post doesnot belong to this user',
            ], 400);
        }
        $images = $request->file('images', []);
        $images_paths = array_map(function($img) use ($post) { 
            if(!$img){
                return response()->json([
                'status'=>'Error',
                "message"=>'image is not found',
            ], 400);
            }
            $path = $this->getStoredImagePath($img,'posts/'.$post->id.'/images');
            $this->imageService->create(['post_id' => $post->id, 'image_path' => $path]);
            return $path;
        }, $images);
        
        if(isset($data['title'])){
            $post->title = $data['title'];
        }
        if(isset($data['body'])){
            $post->body = $data['body'];
        }
        $post->save();
        return response()->json([
            'status' => 'Success',
            "message" => 'post is updated',
            'data' => PostResource::make($post->load('images'))
        ],200);
    }

    /**
     * Delete a post.
     *
     * Permanently deletes a post owned by the authenticated user.
     */
    public function destroy(Post $post)
    {
         $curr_user_id = Auth::id();
        if($post->author->id !== $curr_user_id){
            return response()->json([
                'status'=>'Error',
                "message"=>'post doesnot belong to this user',
            ], 400);
        }
        if(!$post){
            return response()->json(["status"=>'Error', "message"=>'Post doesnot exist'],204);
        }
        $post->delete();
          return response()->noContent();
    }
    /**
     * Delete an image.
     *
     * Permanently deletes an image of a post owned by the authenticated user.
     */
    public function destroyImage(DeletePostImage $request)
    {
        $curr_user_id = Auth::id();
        $data = $request->validated();
        $post = $this->postService->findById($data['post_id']);
        if(!$post){
            return response()->json(["status"=>'Error', "message"=>'Post doesnot exist'],204);
        }

        if($post->author->id !== $curr_user_id){
            return response()->json([
                'status'=>'Error',
                "message"=>'post doesnot belong to this user',
            ], 403);
        }
        foreach ($data['ids'] as $id ) {
           $img = $this->imageService->findById($id);
           if(!$img){
            return response()->json([
                'status'=>'Error',
                "message"=>'image doesnot exist',
            ], 403);
           }
           if($img->post_id !== $post->id){
            return response()->json([
                'status'=>'Error',
                "message"=>'image doesnot belong to this post',
            ], 403);
           }
           $img->delete();
        }
          return response()->noContent();
    }

    /**
     * Create a post from a draft.
     *
     * Publishes an existing draft as a new post.
     */
    public function createPostFromDraft(CreatePostFromDraftRequest $request){
        $data = $request->validated();
        $draft_id = $data['draft_id'];
        $draft = $this->postDraftService->findById((int)($draft_id));
        $post = $this->postService->create([
            'title' => $draft['title'],
            'body' => $draft['body'],
            'description' => $draft['description'],
            'tags' => $draft['tags'],
            'author_id' => $draft['author_id'],
        ]);
        return response()->json([
            'status' => 'Success',
            "message" => 'post is created',
            'data' => PostResource::make($post)
        ]);
    }
}
