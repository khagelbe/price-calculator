<?php

namespace AppBundle\Tests\Service;

use AppBundle\Exceptions\ApiException;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PriceCalculatorTest extends WebTestCase
{
    /** @var Client */
    private $client;

    /**
     * Setup function
     */
    public function setUp()
    {
        $this->client = $client = static::createClient();
    }

    /**
     * @return ContainerInterface
     */
    private function getContainer(): ContainerInterface
    {
        return static::$kernel->getContainer();
    }

    /**
     * Calculates net prices
     *
     * @throws \AppBundle\Exceptions\ApiException
     */
    public function testCalculateNetPrices()
    {
        $products = [
            [
                'name' => 'Milch',
                'price' => 107,
                'taxType' => 1
            ],
            [
                'name' => 'Sojamilch',
                'price' => 119,
                'taxType' => 2
            ],
            [
                'name' => 'Brot',
                'price' => 107,
                'taxType' => 1
            ]
        ];

        $expectedResponse = [
            'products' => [
                [
                    'name' => 'Milch',
                    'price' => 107,
                    'taxType' => 1,
                    'net' => 100
                ],
                [
                    'name' => 'Sojamilch',
                    'price' => 119,
                    'taxType' => 2,
                    'net' => 100
                ],
                [
                    'name' => 'Brot',
                    'price' => 107,
                    'taxType' => 1,
                    'net' => 100
                ]
            ]
        ];

        $resultArray = $this->getContainer()->get('price_calculator')->calculateNetTotal($products);

        $this->assertEquals($resultArray, $expectedResponse);
    }

    /**
     * Calculates gross prices
     *
     * @throws \AppBundle\Exceptions\ApiException
     */
    public function testCalculateGrossPrices()
    {
        $products = [
            [
                'name' => 'Milch',
                'price' => 100,
                'taxType' => 1
            ],
            [
                'name' => 'Sojamilch',
                'price' => 100,
                'taxType' => 2
            ],
            [
                'name' => 'Brot',
                'price' => 100,
                'taxType' => 1
            ]
        ];

        $expectedResponse = [
            'products' => [
                [
                    'name' => 'Milch',
                    'price' => 100,
                    'taxType' => 1,
                    'gross' => 107
                ],
                [
                    'name' => 'Sojamilch',
                    'price' => 100,
                    'taxType' => 2,
                    'gross' => 119
                ],
                [
                    'name' => 'Brot',
                    'price' => 100,
                    'taxType' => 1,
                    'gross' => 107
                ]
            ]
        ];

        $resultArray = $this->getContainer()->get('price_calculator')->calculateGrossTotal($products);

        $this->assertEquals($resultArray, $expectedResponse);
    }

    /**
     * Tries to calculate gross prices for empty list
     *
     * @throws \AppBundle\Exceptions\ApiException
     */
    public function testEmptyProductListGross()
    {
        $products = [];
        $this->expectException(ApiException::class);
        $this->getContainer()->get('price_calculator')->calculateGrossTotal($products);
    }

    /**
     * Tries to calculate net prices for empty list
     *
     * @throws \AppBundle\Exceptions\ApiException
     */
    public function testEmptyProductListNet()
    {
        $products = [];
        $this->expectException(ApiException::class);
        $this->getContainer()->get('price_calculator')->calculateNetTotal($products);
    }

    /**
     * Tries to calculate gross prices with non existing tax type
     *
     * @throws \AppBundle\Exceptions\ApiException
     */
    public function testWrongTaxTypeGross()
    {
        $products = [
            'products' => [
                'name' => 'Milch',
                'price' => 100,
                'taxType' => 12
            ]
        ];

        $this->expectException(ApiException::class);
        $this->getContainer()->get('price_calculator')->calculateGrossTotal($products);
    }

    /**
     * Tries to calculate net prices with non existing tax type
     *
     * @throws \AppBundle\Exceptions\ApiException
     */
    public function testWrongTaxTypeNet()
    {
        $products = [
            'products' => [
                'name' => 'Milch',
                'price' => 100,
                'taxType' => 12
            ]
        ];

        $this->expectException(ApiException::class);
        $this->getContainer()->get('price_calculator')->calculateNetTotal($products);
    }

    /**
     * Tries to calculate net prices with float price
     *
     * @throws \AppBundle\Exceptions\ApiException
     */
    public function testFloatInputNet()
    {
        $products = [
            'products' => [
                'name' => 'Milch',
                'price' => 100.50,
                'taxType' => 12
            ]
        ];

        $this->expectException(ApiException::class);
        $this->getContainer()->get('price_calculator')->calculateNetTotal($products);
    }

    /**
     * Tries to calculate gross prices with float price
     *
     * @throws \AppBundle\Exceptions\ApiException
     */
    public function testFloatInputGross()
    {
        $products = [
            'products' => [
                'name' => 'Milch',
                'price' => 100.50,
                'taxType' => 12
            ]
        ];

        $this->expectException(ApiException::class);
        $this->getContainer()->get('price_calculator')->calculateGrossTotal($products);
    }
}
