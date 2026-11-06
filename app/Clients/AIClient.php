<?php
namespace App\Clients;

use Illuminate\Support\Facades\Http;

class AIClient{
    public function chat(array $messages){
        $response = Http::withToken(config('services.groq.key'))->timeout(60)->post(
            config('services.groq.base_url'). '/chat/completions',
            [
                'model' => config('services.groq.model'),
                'messages' => $messages,
                'response_format' => ['type' => 'json_object'],
                'stream' => false,
            ]
        );

        $response->throw();

        return $response->json();
    }
}