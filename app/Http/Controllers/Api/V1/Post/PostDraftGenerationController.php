<?php

namespace App\Http\Controllers\Api\V1\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\PostDraftGenerationRequest;
use App\Services\AIService;
use App\Http\Resources\PostDraftResource;
use App\Services\PostDraftService;
use Illuminate\Http\Request;
use Dedoc\Scramble\Attributes\Group;

#[Group('Post drafts', description: 'List drafts and generate new ones with AI.')]
class PostDraftGenerationController extends Controller
{
    public function __construct(private AIService $aiService, private PostDraftService $postDraftService){}

    /**
     * List post drafts.
     *
     * Returns drafts belonging to the authenticated user.
     */
    public function index(Request $request){
        $user = $request->user();
        $postDrafts = $this->postDraftService->findAll(['id' => $user->id]) ;
        return response()->json([
                'status' => 'Success',
                'message' => 'Post drafts retrieved successfully',
                'data' => PostDraftResource::collection($postDrafts)
            ], 200);

    }

    /**
     * Generate a post draft.
     *
     * Uses AI to create a draft from a topic, optional image, and instructions.
     */
    public function store(PostDraftGenerationRequest $request){
        $image = $request->file('image');
        $postDraftData = [];
        $imagePath = '';
        if($image){

            $imgOriginalName = $image->getClientOriginalName();
    
            $safeName = preg_replace('/[^0-9a-zA-Z.-_]/', '_', pathinfo($imgOriginalName, PATHINFO_FILENAME));
            $extension = pathinfo($imgOriginalName, PATHINFO_EXTENSION);
    
            $imgName = $safeName . '_' . now()->format('Y.m.d-H:i'). "." . $extension;
            $imagePath = $image->storeAs('uploads/ai/images',$imgName, 'public');
            $postDraftData = ['image' => $imagePath];
        };

      try{  
        $data = $request->validated();
        $topic = $data['topic'];
        $instructions = $data['instructions'] ?? [];
        $lang = $data['lang'];
        $tone = $data['tone'] ?? null;

        $response = $this->aiService->generatePostDraft($image, $topic, $instructions, $lang, $tone);

        $postDraftData = [
            ...$postDraftData,
            'author_id' => $request->user()->id,
            'title' => $response['title'],
            'description' => $response['description'],
            'body' => $response['body'],
            'image_alt' => $response['image_alt'],
            'tags' => $response['tags'],
        ];
        if(!empty($postDraftData)){
            $postDraft = $this->postDraftService->create($postDraftData);
            return response()->json([
                'status' => 'Success',
                'message' => 'Post draft created successfully',
                'postDraft' => PostDraftResource::make($postDraft)
            ], 201);
        }
        return response()->json([
                'status' => 'Error',
                'message' => 'Something went wrong while creating the post draft',
            ], 400);
        }catch(\Throwable $e){
            if ($imagePath) {
            Storage::disk('public')->delete($imagePath);
            }
            if ($e->getCode() === 429) {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'AI service is temporarily busy. Please try again later.',
                ], 429);
            }
         throw $e;

      }
    }
}
