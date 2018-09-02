<?php

namespace AppBundle\Controller;

use AppBundle\Exceptions\ApiException;
use AppBundle\Form\ProductListType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * Calculates net prices for product array
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function calculateNetAction(Request $request): JsonResponse
    {
        $form = $this->createForm(ProductListType::class, $request->get('products'));

        $form->handleRequest($request);

        // TODO Handle form errors more specific
        if (!$form->isValid()) {
            $exception = new ApiException(ApiException::FORM_NOT_VALID);
            return new JsonResponse($exception->getApiErrorData(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $products = $form->getData();

        try {
            $data = $this->container->get('price_calculator')->calculateNetTotal($products);
        } catch (ApiException $e) {
            return new JsonResponse($e->getApiErrorData(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * Calculates gross prices for product array
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function calculateGrossAction(Request $request): JsonResponse
    {
        $form = $this->createForm(ProductListType::class, $request->get('products'));

        $form->handleRequest($request);

        if (!$form->isValid()) {
            $exception = new ApiException(ApiException::FORM_NOT_VALID);
            return new JsonResponse($exception->getApiErrorData(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $products = $form->getData();

        try {
            $data = $this->container->get('price_calculator')->calculateGrossTotal($products);
        } catch (ApiException $e) {
            return new JsonResponse($e->getApiErrorData(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }
}
