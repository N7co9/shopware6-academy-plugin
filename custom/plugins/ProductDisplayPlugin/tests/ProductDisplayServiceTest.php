<?php
declare(strict_types=1);

namespace ProductDisplayPlugin\Tests;

use PHPUnit\Framework\TestCase;
use ProductDisplayPlugin\ProductDisplayService;
use RuntimeException;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ProductDisplayServiceTest extends TestCase
{
    private HttpClientInterface $httpClient;
    private SystemConfigService $systemConfigService;
    private ProductDisplayService $productDisplayService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->systemConfigService = $this->createMock(SystemConfigService::class);

        $this->productDisplayService = new ProductDisplayService(
            $this->httpClient,
            $this->systemConfigService
        );
    }

    public function testFetchProductsThrowsExceptionWhenNoAccessToken(): void
    {
        $this->systemConfigService
            ->method('get')
            ->with('ProductDisplayPlugin.config.accessToken')
            ->willReturn(null);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Access token is not configured.');

        $this->productDisplayService->fetchProducts();
    }

    public function testFetchProductsThrowsExceptionOnNon200Response(): void
    {
        $this->systemConfigService
            ->method('get')
            ->with('ProductDisplayPlugin.config.accessToken')
            ->willReturn('testAccessToken');

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getStatusCode')->willReturn(400);

        $this->httpClient
            ->method('request')
            ->willReturn($responseMock);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Failed to fetch products. Status code: 400');

        $this->productDisplayService->fetchProducts();
    }

    public function testFetchProductsReturnsArrayOnSuccess(): void
    {
        $this->systemConfigService
            ->method('get')
            ->with('ProductDisplayPlugin.config.accessToken')
            ->willReturn('testAccessToken');

        $responseArray = [
            'elements' => [
                [
                    'translated' => [
                        'name' => 'My Product',
                        'description' => 'Product Description'
                    ],
                    'active' => true,
                    'productNumber' => 'P12345',
                    'availableStock' => 50,
                    'calculatedPrice' => [
                        'unitPrice' => 19.99
                    ]
                ],
            ],
        ];

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getStatusCode')->willReturn(200);
        $responseMock->method('toArray')->willReturn($responseArray);

        $this->httpClient
            ->method('request')
            ->willReturn($responseMock);

        // Act
        $fetchedData = $this->productDisplayService->fetchProducts();

        // Assert
        $this->assertIsArray($fetchedData);
        $this->assertArrayHasKey('elements', $fetchedData);
        $this->assertCount(1, $fetchedData['elements']);
    }

    public function testActionThrowsExceptionOnEmptyResponse(): void
    {
        $this->systemConfigService
            ->method('get')
            ->with('ProductDisplayPlugin.config.accessToken')
            ->willReturn('testAccessToken');

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getStatusCode')->willReturn(200);
        $responseMock->method('toArray')->willReturn([]);

        $this->httpClient
            ->method('request')
            ->willReturn($responseMock);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('API Call succeeded but returned an empty products array');

        $this->productDisplayService->action();
    }

    public function testActionReturnsAlignedArray(): void
    {
        $this->systemConfigService
            ->method('get')
            ->with('ProductDisplayPlugin.config.accessToken')
            ->willReturn('testAccessToken');

        $responseArray = [
            'elements' => [
                [
                    'translated' => [
                        'name' => 'My Product',
                        'description' => 'Product Description'
                    ],
                    'active' => true,
                    'productNumber' => 'P12345',
                    'availableStock' => 50,
                    'calculatedPrice' => [
                        'unitPrice' => 19.99
                    ]
                ],
            ],
        ];

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getStatusCode')->willReturn(200);
        $responseMock->method('toArray')->willReturn($responseArray);

        $this->httpClient
            ->method('request')
            ->willReturn($responseMock);

        // Act
        $products = $this->productDisplayService->action();

        // Assert
        $this->assertCount(1, $products);
        $this->assertArrayHasKey('title', $products[0]);
        $this->assertArrayHasKey('description', $products[0]);
        $this->assertArrayHasKey('active', $products[0]);
        $this->assertArrayHasKey('productNumber', $products[0]);
        $this->assertArrayHasKey('availableStock', $products[0]);
        $this->assertArrayHasKey('Price', $products[0]);
        $this->assertEquals('My Product', $products[0]['title']);
    }

    public function testAlignContentBuildsCorrectStructure(): void
    {
        $inputArray = [
            'elements' => [
                [
                    'translated' => [
                        'name' => 'Test Product',
                        'description' => 'A cool product'
                    ],
                    'active' => false,
                    'productNumber' => 'T98765',
                    'availableStock' => 100,
                    'calculatedPrice' => [
                        'unitPrice' => 49.99
                    ]
                ],
            ],
        ];

        // Act
        $output = $this->productDisplayService->alignContent($inputArray);

        // Assert
        $this->assertCount(1, $output);
        $this->assertEquals('Test Product', $output[0]['title']);
        $this->assertEquals('A cool product', $output[0]['description']);
        $this->assertFalse($output[0]['active']);
        $this->assertEquals('T98765', $output[0]['productNumber']);
        $this->assertEquals(100, $output[0]['availableStock']);
        $this->assertEquals(49.99, $output[0]['Price']);
    }
}