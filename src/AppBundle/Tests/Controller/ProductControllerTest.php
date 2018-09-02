<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProductControllerTest extends WebTestCase
{
    /** @var Client */
    private $client;

    /**
     * Setup function
     */
    public function setUp()
    {
        $this->client = static::createClient();
    }

    /**
     * Calculates gross price
     */
    public function testCalculateGrossPrices()
    {
        $requestBody = [
            'products' => [
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

        $this->client->request('POST', '/product/calculate-gross', $requestBody);

        $this->assertEquals(
            Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode()
        );

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertEquals($response, $expectedResponse);
    }

    /**
     * Calculates gross price
     */
    public function testCalculateNetPrices()
    {
        $requestBody = [
            'products' => [
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

        $this->client->request('POST', '/product/calculate-net', $requestBody);

        $this->assertEquals(
            Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode()
        );

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertEquals($response, $expectedResponse);
    }

    /**
     * DAta provider for all tested error cases
     *
     * @return array
     */
    public function errorCaseDataProvider(): array
    {
        return [
            'Empty array' => [
                'requestBody' => [
                    'products' => [

                    ]
                ],
                'expectedResponse' => [
                    'errorCode' => 112,
                    'message' => 'Product not found'
                ]
            ],
            'Tax type not found' => [
                'requestBody' => [
                    'products' => [
                        [
                            'name' => 'Milch',
                            'price' => 107,
                            'taxType' => 12
                        ]
                    ]
                ],
                'expectedResponse' => [
                    'errorCode' => 113,
                    'message' => 'Tax type not found'
                ]
            ],
            'Price cannot be calculated' => [
                'requestBody' => [
                    'products' => [
                        [
                            'name' => 'Milch',
                            'price' => 0,
                            'taxType' => 1
                        ]
                    ]
                ],
                'expectedResponse' => [
                    'errorCode' => 111,
                    'message' => 'Price cannot be calculated'
                ]
            ],
            'Invalid form' => [
                'requestBody' => [
                    'products' => [
                        [
                            'name' => 'Milch',
                            'price' => 'Milch',
                            'taxType' => 1
                        ]
                    ]
                ],
                'expectedResponse' => [
                    'errorCode' => 110,
                    'message' => 'Form is not valid'
                ]
            ],
            'Prices in floats' => [
                'requestBody' => [
                    'products' => [
                        [
                            'name' => 'Milch',
                            'price' => 10.5,
                            'taxType' => 1
                        ]
                    ]
                ],
                'expectedResponse' => [
                    'errorCode' => 111,
                    'message' => 'Price cannot be calculated'
                ]
            ]
        ];
    }

    /**
     * Tries to calculate net prices with error
     *
     * @param array $requestBody
     * @param array $expectedResponse
     *
     * @dataProvider errorCaseDataProvider
     */
    public function testCalculateNetWithError(array $requestBody, array $expectedResponse)
    {
        $this->client->request('POST', '/product/calculate-net', $requestBody);

        $this->assertEquals(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $this->client->getResponse()->getStatusCode()
        );

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertEquals($response, $expectedResponse);
    }

    /**
     * Tries to calculate gross prices with error
     *
     * @param array $requestBody
     * @param array $expectedResponse
     *
     * @dataProvider errorCaseDataProvider
     */
    public function testCalculateGrossWithError(array $requestBody, array $expectedResponse)
    {
        $this->client->request('POST', '/product/calculate-gross', $requestBody);

        $this->assertEquals(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $this->client->getResponse()->getStatusCode()
        );

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertEquals($response, $expectedResponse);
    }
}
