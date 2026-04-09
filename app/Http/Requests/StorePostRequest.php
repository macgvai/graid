<?php

namespace App\Http\Requests;

use App\Enums\PostType;
use App\Services\Posts\HashtagNormalizer;
use App\Services\Posts\YouTubeUrlParser;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(HashtagNormalizer $hashtagNormalizer, YouTubeUrlParser $youTubeUrlParser): array
    {
        $postType = $this->postType();
        $hasUploadedImage = $this->file('image_file') !== null;

        return [
            'post_type' => ['required', 'integer', Rule::in(array_map(
                static fn (PostType $type): int => $type->value,
                PostType::cases(),
            ))],
            'title' => ['required', 'string', 'max:255'],
            'tags' => [
                'required',
                'string',
                static function (string $attribute, mixed $value, Closure $fail) use ($hashtagNormalizer): void {
                    if (! is_string($value) || ! $hashtagNormalizer->isValid($value)) {
                        $fail('Теги должны состоять из отдельных слов, разделённых пробелами.');
                    }
                },
            ],
            'image_file' => [
                Rule::excludeIf($postType !== PostType::Photo),
                'nullable',
                'file',
                'mimes:png,jpg,jpeg,gif',
            ],
            'image_url' => [
                Rule::excludeIf($postType !== PostType::Photo || $hasUploadedImage),
                'nullable',
                'url',
            ],
            'video' => [
                Rule::requiredIf($postType === PostType::Video),
                Rule::excludeIf($postType !== PostType::Video),
                'nullable',
                'url',
                static function (string $attribute, mixed $value, Closure $fail) use ($youTubeUrlParser): void {
                    $isInvalidYouTubeUrl = $value !== null
                        && (! is_string($value) || $youTubeUrlParser->extractVideoId($value) === null);

                    if ($isInvalidYouTubeUrl) {
                        $fail('Укажите ссылку на видео с YouTube.');
                    }
                },
            ],
            'text_content' => [
                Rule::requiredIf(in_array($postType, [PostType::Text, PostType::Quote], true)),
                Rule::excludeIf(! in_array($postType, [PostType::Text, PostType::Quote], true)),
                'nullable',
                'string',
            ],
            'quote_author' => [
                Rule::requiredIf($postType === PostType::Quote),
                Rule::excludeIf($postType !== PostType::Quote),
                'nullable',
                'string',
                'max:255',
            ],
            'link' => [
                Rule::requiredIf($postType === PostType::Link),
                Rule::excludeIf($postType !== PostType::Link),
                'nullable',
                'url',
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($this->postType() !== PostType::Photo) {
                return;
            }

            if ($this->file('image_file') !== null || $this->filled('image_url')) {
                return;
            }

            $message = 'Загрузите файл изображения или укажите прямую ссылку на изображение.';

            $validator->errors()->add('image_file', $message);
            $validator->errors()->add('image_url', $message);
        });
    }

    public function attributes(): array
    {
        return [
            'title' => 'Заголовок',
            'tags' => 'Теги',
            'image_file' => 'Выбор файла',
            'image_url' => 'Ссылка из интернета',
            'video' => 'Ссылка на YouTube',
            'text_content' => 'Текст',
            'quote_author' => 'Автор',
            'link' => 'Ссылка',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Поле «:attribute» обязательно для заполнения.',
            'url' => 'Поле «:attribute» должно содержать корректный URL.',
            'mimes' => 'Поле «:attribute» должно быть изображением формата png, jpeg или gif.',
        ];
    }

    public function postType(): ?PostType
    {
        return PostType::tryFrom($this->resolvePostTypeValue());
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'post_type' => $this->resolvePostTypeValue(),
            'title' => $this->normalize($this->input('title')),
            'tags' => $this->normalize($this->input('tags')),
            'image_url' => $this->normalize($this->input('image_url')),
            'video' => $this->normalize($this->input('video')),
            'text_content' => $this->normalize($this->input('text_content')),
            'quote_author' => $this->normalize($this->input('quote_author')),
            'link' => $this->normalize($this->input('link')),
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

    private function resolvePostTypeValue(): int
    {
        $routeType = $this->route('type');

        if (is_int($routeType) || is_string($routeType)) {
            return (int) $routeType;
        }

        $inputType = $this->input('post_type');

        return is_int($inputType) || is_string($inputType)
            ? (int) $inputType
            : 0;
    }
}
