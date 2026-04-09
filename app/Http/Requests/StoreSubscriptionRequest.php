<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'author_id' => ['required', 'integer', 'exists:users,id'],
            'target_id' => [
                'required',
                'integer',
                'exists:users,id',
                'different:author_id',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'author_id' => $this->user()?->getKey(),
            'target_id' => $this->route('user')?->getKey(),
        ]);
    }
}
