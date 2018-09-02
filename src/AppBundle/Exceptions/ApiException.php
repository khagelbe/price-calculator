<?php

namespace AppBundle\Exceptions;

use Exception;

class ApiException extends Exception
{
    const FORM_NOT_VALID = 110;
    const PRICE_CANNOT_BE_CALCULATED = 111;
    const PRODUCT_NOT_FOUND = 112;
    const TAX_TYPE_NOT_FOUND = 113;

    /**
     * ApiException constructor.
     * @param int $errorCode
     */
    public function __construct(int $errorCode)
    {
        $errorMessage = $this->getErrorMessage($errorCode);

        parent::__construct($errorMessage, $errorCode);
    }

    /**
     * @return array
     */
    public function getApiErrorData()
    {
        return [
            'errorCode' => $this->getCode(),
            'message' => $this->getMessage()
        ];
    }

    /**
     * @param int $errorCode
     * @return string
     */
    private function getErrorMessage(int $errorCode)
    {
        switch ($errorCode) {
            case self::FORM_NOT_VALID:
                return "Form is not valid";
            case self::PRICE_CANNOT_BE_CALCULATED:
                return "Price cannot be calculated";
            case self::PRODUCT_NOT_FOUND:
                return "Product not found";
            case self::TAX_TYPE_NOT_FOUND:
                return "Tax type not found";
            default:
                return "Something went wrong";
        }
    }
}
