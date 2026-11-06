<?php

namespace App\Http\Requests\Post;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
            'title' => 'required|min:5',
            'body' => 'required|min:25',
            'images' => [
                'sometimes',
                'array',
                'max:4'
            ],
            'images.*' => [
                'sometimes',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ]
        ];
    }
    public function messages(){
        return [
            'title.required' => 'Title is required',
            'title.min' => 'Title should be 5 characters atleast',
            'author_id.required' => 'author_id is required',
            'body.required' => 'Body is required',
            'body.min' => 'Body should be 25 chars atleast',
            // 'tags' => 'array',
            // 'tags.*'=>'string'
        ];
    }
}
