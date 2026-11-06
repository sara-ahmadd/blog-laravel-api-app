<?php

namespace App\Http\Controllers\Api\V1\Comment;

use App\Http\Controllers\Controller;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Services\PostService;
use App\Services\CommentService;
use App\Services\PostDraftService;
use App\Services\ImageService;
use App\Http\Requests\CreateCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;


class CommentController extends Controller
{
    public function __construct(private PostService $postService, private ImageService $imageService,private CommentService $commentService){}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Post $post)
    {
        $user = $request->user();
        $comments = $this->commentService->findAll(['post_id' => $post->id]);
        return response()->json([
            'status' => 'Success',
            "message" => 'comments retrieved',
            'data' => CommentResource::collection($comments->load('replies'))
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCommentRequest $request, Post $post){
    
        $data = $request->validated();
        $user = $request->user();
        $comment = $this->commentService->create([
            'post_id' => $post->id,
            'user_id' => $user->id,
            'content' => $data['content']
        ]);
        return response()->json([
            'status' => 'Success',
            "message" => 'comment created',
            'data' => CommentResource::make($comment->load(['post','user']))
        ],201);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function reply(CreateCommentRequest $request, Post $post, Comment $comment)
    {
        $data = $request->validated();
        $user = $request->user();
        $comment = $this->commentService->create([
            'post_id' => $post->id,
            'user_id' => $user->id,
            'content' => $data['content'],
            'parent_id' => $comment->id
        ]);
        return response()->json([
            'status' => 'Success',
            "message" => "reply to the comment : " . $comment->id . " created",
            'data' => CommentResource::make($comment->load(['post','user','replies']))
        ],201);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentRequest $request, Post $post,Comment $comment)
    {
       
        $user = $request->user();
        if($comment->user->id !== $user->id ){
            return response()->json([
                'status' => 'Error',
                "message" => "you're not authorized to update this comment"
            ],403);
        }
        $data = $request->validated();
        $comment->update(['content' => $data['content']]);
        return response()->json([
            'status' => 'Success',
            "message" => "comment updated",
            "comment" => $comment->fresh()
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Comment $comment)
    {
        $user = $request->user();
        if($comment->user->id !== $user->id ){
            return response()->json([
                'status' => 'Error',
                "message" => "you're not authorized to delete this comment"
            ],403);
        }
        $comments = $this->commentService->delete($comment);
        return response()->noContent();
    }
}
