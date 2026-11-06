<?php

namespace App\Http\Requests\Post;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
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
                'title' => [
                    'required_without_all:body,images',
                    'string',
                    'min:5',
                ],

                'body' => [
                    'required_without_all:title,images',
                    'string',
                    'min:25',
                ],

                'images' => [
                    'required_without_all:title,body',
                    'array',
                    'min:1',
                    'max:4',
                ],

                'images.*' => [
                    'file',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:2048',
                ],
            ];
        }
}
