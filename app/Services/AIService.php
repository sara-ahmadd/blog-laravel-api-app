<?php
namespace App\Services;

use Illuminate\Http\UploadedFile;
use App\Clients\AIClient;


class AIService{
   public function __construct(private AIClient $client){}

    public function generatePostDraft(?UploadedFile $image, ?string $topic, array $instructions,string $lang, ?string $tone ){

        $prompt = $this->buildPrompt(
            $topic, $instructions, $lang, $tone
        );
        
        $data =  [
            'role' => 'user',
            'content' => [
                [
                    'type' => 'text',
                    'text' => $prompt,
                ],
            ],
        ];
        
        if($image){
            $imageData = base64_encode(file_get_contents($image->getPathname()));
            $mimeType = $image->getMimeType();
            $data['content'][] = [
                        'type' => 'image_url',
                        'image_url' => [
                            'url' => "data:{$mimeType};base64,{$imageData}",
                        ],
                    ];
    
        }

        $response = $this->client->chat([ $data ]);
        $content = $response['choices'][0]['message']['content'] ?? null;

        if (!is_string($content) || trim($content) === '') {
            throw new RuntimeException('AI returned an empty response.');
        }

        $result = json_decode(
            $content,
            true,
            512,
            JSON_THROW_ON_ERROR
        );
     
        return $result ;
    }

    private function buildPrompt(
    ?string $topic,
    array $instructions,
    string $lang,
    ?string $tone = 'Professional'
): string {

    $instructionsText = implode("\n", $instructions);

    return "
        You are an AI blog writing assistant.

        Analyze the provided image and generate a blog post draft.

        Topic:
        {$topic}

        Language:
        {$lang}

        Writing tone:
        {$tone}

        Additional instructions:
        {$instructionsText}

        Generate the following:

        1. A suitable title.
        2. A short description.
        3. A complete blog post body.
        4. Relevant tags.
        5. Image alt text.

        Do not invent facts or personal experiences
        that cannot be inferred from the image or user input.

        
        Return only a valid JSON object, with no Markdown fences or other text,
        using these fields:
        {
            \"title\": \"...\",
            \"description\": \"...\",
            \"body\": \"...\",
            \"tags\": [],
            \"image_alt\": \"...\"
        }

    ";
}
}