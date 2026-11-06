<?php

namespace App\Http\Requests\Post;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PostDraftGenerationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'image' => [
                'sometimes', //optional,but if sent -> validate it
                'image',
                'file', 
                'mimes:png,jpeg,jpg,svg,gif',
                'max:10240', // 10MB
                'min:1',
                'dimensions:min_width=100,min_height=100,max_width=10000,max_height=10000'
                ],
            'topic' => ['required', 'string', 'min:5'],
            'instructions' => ['sometimes', 'array', 'min:100'],
            'instructions.*' => [ 'sometimes','string', 'min:5'],
            'lang' => ['required', 'string', 'min:1'],
            'tone' => ['sometimes','string'],
        ];
    }
}
