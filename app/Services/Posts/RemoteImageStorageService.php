<?php

namespace App\Services\Posts;

use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RemoteImageStorageService
{
    public function __construct(
        private readonly HttpFactory $httpFactory,
        private readonly FilesystemFactory $filesystemFactory,
    ) {
    }

    public function downloadAndStore(string $url, string $directory, string $field, string $errorMessage): string
    {
        try {
            $response = $this->httpFactory
                ->timeout(20)
                ->retry(1, 200)
                ->get($url);
        } catch (\Throwable) {
            throw ValidationException::withMessages([
                $field => $errorMessage,
            ]);
        }

        if (! $response->successful() || $response->body() === '') {
            throw ValidationException::withMessages([
                $field => $errorMessage,
            ]);
        }

        return $this->storeBinaryImage($response->body(), $directory, $field);
    }

    public function storeBinaryImage(string $contents, string $directory, string $field): string
    {
        $imageInfo = @getimagesizefromstring($contents);

        if ($imageInfo === false) {
            throw ValidationException::withMessages([
                $field => 'Полученный файл не является изображением.',
            ]);
        }

        $extension = match ($imageInfo['mime']) {
            'image/png' => 'png',
            'image/jpeg' => 'jpg',
            'image/gif' => 'gif',
            default => null,
        };

        if ($extension === null) {
            throw ValidationException::withMessages([
                $field => 'Допустимы только изображения формата png, jpeg или gif.',
            ]);
        }

        $path = sprintf('%s/%s.%s', trim($directory, '/'), (string) Str::uuid(), $extension);
        $this->filesystemFactory->disk('public')->put($path, $contents);

        return $path;
    }
}
