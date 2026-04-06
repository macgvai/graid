<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

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
//            'user_id' => 'required|exists:users,id',
//            'content_type_id' => 'required|exists:content_types,id',
            'title' => 'nullable|string',
            'text_content' => 'nullable|string',
            'quote_author' => 'nullable|string',
            'image' => 'nullable|string',
            'video' => 'nullable|string',
            'link' => 'nullable|string'
        ];
    }
}
