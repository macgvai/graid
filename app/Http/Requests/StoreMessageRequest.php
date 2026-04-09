<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'sender_id' => ['required', 'integer', 'exists:users,id'],
            'receiver_id' => [
                'required',
                'integer',
                'exists:users,id',
                'different:sender_id',
            ],
            'content' => ['required', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'sender_id' => $this->user()?->getKey(),
            'content' => $this->normalize($this->input('content')),
        ]);
    }

    private function normalize(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $normalized = trim($value);

        return $normalized === '' ? null : $normalized;
    }
}
