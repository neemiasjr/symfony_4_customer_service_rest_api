<?php


namespace App\Controller;

use App\Entity\City;
use App\Service\CityService;
use App\Service\ResponseErrorDecoratorService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CityController extends Controller
{
    /**
     * Creates new city by passed JSON data
     *
     * @Route("/api/cities")
     * @Method("POST")
     * @param Request $request
     * @param CityService $cityService
     * @param ResponseErrorDecoratorService $errorDecorator
     * @return JsonResponse
     */
    public function createCity(
        Request $request,
        CityService $cityService,
        ResponseErrorDecoratorService $errorDecorator
    )
    {
        $body = $request->getContent();
        $data = json_decode($body, true);

        if (is_null($data)) {
            $status = JsonResponse::HTTP_BAD_REQUEST;
            $data = $errorDecorator->decorateError(
                JsonResponse::HTTP_BAD_REQUEST, "Invalid JSON format"
            );

            return new JsonResponse($data, $status);
        }

        $result = $cityService->createCity($data);
        if ($result instanceof City) {
            $status = JsonResponse::HTTP_CREATED;
            $data = [
                'data' => [
                    'city_id' => $result->getId(),
                    'name' => $result->getName(),
                ]
            ];
        } else {
            $status = JsonResponse::HTTP_BAD_REQUEST;
            $data = $errorDecorator->decorateError($status, $result);
        }

        return new JsonResponse($data, $status);
    }

    /**
     * Update city by passed JSON data
     *
     * @Route("/api/cities/{id}")
     * @Method("PUT")
     * @param City $city
     * @param Request $request
     * @param CityService $cityService
     * @param ResponseErrorDecoratorService $errorDecorator
     * @return JsonResponse
     */
    public function updateCity(
        City $city,
        Request $request,
        CityService $cityService,
        ResponseErrorDecoratorService $errorDecorator
    )
    {
        $body = $request->getContent();
        $data = json_decode($body, true);

        if (is_null($data)) {
            $status = JsonResponse::HTTP_BAD_REQUEST;
            $data = $errorDecorator->decorateError(
                JsonResponse::HTTP_BAD_REQUEST, "Invalid JSON format"
            );

            return new JsonResponse($data, $status);
        }

        $result = $cityService->updateCity($city, $data);
        if ($result instanceof City) {
            $status = JsonResponse::HTTP_OK;
            $data = [
                'data' => [
                    'city_id' => $result->getId(),
                    'name' => $result->getName(),
                ]
            ];
        } else {
            $status = JsonResponse::HTTP_BAD_REQUEST;
            $data = $errorDecorator->decorateError($status, $result);
        }

        return new JsonResponse($data, $status);
    }

    /**
     * @Route("/api/cities/{id}")
     * @Method("DELETE")
     * @param City $city
     * @param CityService $cityService
     * @param ResponseErrorDecoratorService $errorDecorator
     * @return JsonResponse
     */
    public function deleteCity(
        City $city,
        CityService $cityService,
        ResponseErrorDecoratorService $errorDecorator
    )
    {
        $result = $cityService->deleteCity($city);
        if ($result === true) {
            $status = JsonResponse::HTTP_NO_CONTENT;
            $data = null;
        } else {
            $status = JsonResponse::HTTP_BAD_REQUEST;
            $data = $errorDecorator->decorateError($status, $result);
        }

        return new JsonResponse($data, $status);
    }

    /**
     * @Route("/api/cities/{id}")
     * @Method("GET")
     * @param City $city Symfony will find city entity by {id} and will assign it to $city
     * @return JsonResponse Data array which contains information about city
     */
    public function getCity(City $city)
    {
        $status = JsonResponse::HTTP_OK;
        $data = [
            'data' => [
                'city' => [
                    'city_id' => $city->getId(),
                    'name' => $city->getName()
                ]
            ]
        ];

        return new JsonResponse($data, $status);
    }

    /**
     * @Route("/api/cities")
     * @Method("GET")
     * @param CityService $cityService
     * @return JsonResponse List of cities
     */
    public function getCities(CityService $cityService)
    {
        $cities = $cityService->getCities();
        $citiesArr = [];
        foreach ($cities as $city) {
            $citiesArr[] = [
                'city_id' => $city->getId(),
                'name' => $city->getName(),
            ];
        }

        $status = JsonResponse::HTTP_OK;
        $data = [
            'data' => [
                'cities' => $citiesArr
            ]
        ];

        return new JsonResponse($data, $status);
    }
}