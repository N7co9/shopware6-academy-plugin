<?php
declare(strict_types=1);

namespace ProductDisplayPlugin;

use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ProductDisplayService
{
    public const string ENDPOINT = 'http://localhost/store-api/product';

    public function __construct(
        private HttpClientInterface $client,
        private SystemConfigService $systemConfigService
    )
    {
    }

    public function action(): array
    {
        $response = $this->fetchProducts();

        if (empty($response)) {
            throw new \RuntimeException('API Call succeeded but returned an empty products array');
        }

        return $this->alignContent($response);
    }

    public function fetchProducts(): array
    {
        $accessToken = $this->systemConfigService->get('ProductDisplayPlugin.config.accessToken');

        if (!$accessToken) {
            throw new \RuntimeException('Access token is not configured.');
        }

        $response = $this->client->request('GET', self::ENDPOINT, [
            'headers' => [
                'sw-access-key' => $accessToken,
                'Content-Type' => 'application/json',
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Failed to fetch products. Status code: ' . $response->getStatusCode());
        }

        return $response->toArray();
    }

    public function alignContent(array $response): array
    {
        $products = [];

        foreach ($response['elements'] as $element) {
            $products [] = [
                'title' => $element['translated']['name'],
                'description' => $element['translated']['description'],
                'active' => $element['active'],
                'productNumber' => $element['productNumber'],
                'availableStock' => $element['availableStock'],
                'Price' => $element['calculatedPrice']['unitPrice']
            ];
        }
        return $products;
    }
}