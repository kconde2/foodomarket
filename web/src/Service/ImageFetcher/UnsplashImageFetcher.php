<?php

declare(strict_types=1);

namespace App\Service\ImageFetcher;

use App\Mercuriale\Domain\Model\Product\Service\ImageFetcherInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UnsplashImageFetcher implements ImageFetcherInterface
{
    private const UNSPLASH_API_URL = 'https://api.unsplash.com/search/photos';

    private string $unsplashAccessKey;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly CacheInterface $cache,
        private readonly ParameterBagInterface $params,
    ) {
        $this->unsplashAccessKey = $this->params->get('unsplash.application_key');
    }

    public function fetchImageUrl(string $query): ?string
    {
        $data = $this->cache->get('unsplash_image_'.md5($query), function (ItemInterface $item) use ($query) {
            // Set cache expiration to 1 day (86400 seconds)
            $item->expiresAfter(86400);

            $response = $this->httpClient->request('GET', self::UNSPLASH_API_URL, [
                'query' => [
                    'query' => $query,
                    'client_id' => $this->unsplashAccessKey,
                    'orientation' => 'landscape',
                ],
            ]);

            if (200 !== $response->getStatusCode()) {
                return null;
            }

            $data = $response->toArray();

            return $data['results'][0]['urls']['regular'] ?? null;
        });

        return $data;
    }
}
