<?php

namespace App\Services\Posts;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Validation\ValidationException;

class LinkPreviewService
{
    public function __construct(
        private readonly HttpFactory $httpFactory,
        private readonly RemoteImageStorageService $remoteImageStorageService,
        private readonly Repository $config,
    ) {
    }

    public function fetchAndStore(string $link): string
    {
        $apiKey = $this->config->get('services.thumbnail_ws.key');

        if (! is_string($apiKey) || $apiKey === '') {
            throw ValidationException::withMessages([
                'link' => 'Не настроен ключ для сервиса thumbnail.ws.',
            ]);
        }

        try {
            $response = $this->httpFactory
                ->timeout(20)
                ->retry(1, 200)
                ->get(sprintf('https://api.thumbnail.ws/api/%s/thumbnail/get', $apiKey), [
                    'url' => $link,
                    'width' => $this->config->get('services.thumbnail_ws.width', 640),
                ]);
        } catch (\Throwable) {
            throw ValidationException::withMessages([
                'link' => 'Не удалось получить превью ссылки.',
            ]);
        }

        if (! $response->successful() || $response->body() === '') {
            throw ValidationException::withMessages([
                'link' => 'Не удалось получить превью ссылки.',
            ]);
        }

        return $this->remoteImageStorageService->storeBinaryImage($response->body(), 'posts/previews', 'link');
    }
}
