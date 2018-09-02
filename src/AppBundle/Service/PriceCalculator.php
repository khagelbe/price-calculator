<?php
namespace AppBundle\Service;

use AppBundle\Exceptions\ApiException;

/**
 * Class PriceCalculator
 *
 * @package AppBundle\Service
 */
class PriceCalculator
{
    private const VAT_TYPE_LOW = 1;
    private const VAT_TYPE_HIGH = 2;
    private const VAT_LOW = 7;
    private const VAT_HIGH = 19;

    /**
     * @param array $products
     *
     * @return array
     * @throws ApiException
     */
    public function calculateGrossTotal(array $products): array
    {
        if (!$products) {
            throw new ApiException(ApiException::PRODUCT_NOT_FOUND);
        }

        foreach ($products as &$product) {

            if ($product['price'] == 0 || is_float($product['price'])) {
                throw new ApiException(ApiException::PRICE_CANNOT_BE_CALCULATED);
            }

            $taxPercent = $this->checkVat($product['taxType']);
            $product['gross'] = floor((100 + ($taxPercent)) / 100 * $product['price']);
        }

        return ['products' => $products];
    }

    /**
     * @param array $products
     *
     * @return array
     * @throws ApiException
     */
    public function calculateNetTotal(array $products): array
    {
        if (!$products) {
            throw new ApiException(ApiException::PRODUCT_NOT_FOUND);
        }

        foreach ($products as &$product) {

            if ($product['price'] == 0 || is_float($product['price'])) {
                throw new ApiException(ApiException::PRICE_CANNOT_BE_CALCULATED);
            }

            $taxPercent = $this->checkVat($product['taxType']);
            $product['net'] = floor($product['price'] * 100 / ($taxPercent + 100));
        }

        return ['products' => $products];
    }

    /**
     * @param int $taxType
     *
     * @return int
     * @throws ApiException
     */
    private function checkVat(int $taxType): int
    {
        if ($taxType === self::VAT_TYPE_LOW) {
            return self::VAT_LOW;
        } else if ($taxType === self::VAT_TYPE_HIGH) {
            return self::VAT_HIGH;
        } else {
            throw new ApiException(ApiException::TAX_TYPE_NOT_FOUND);
        }
    }
}
