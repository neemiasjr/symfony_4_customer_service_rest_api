<?php


namespace App\Controller;

use App\Entity\Period;
use App\Service\PeriodService;
use App\Service\ResponseErrorDecoratorService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PeriodController extends Controller
{
    /**
     * Creates new period by passed JSON data
     *
     * @Route("/api/periods")
     * @Method("POST")
     * @param Request $request
     * @param PeriodService $periodService
     * @param ResponseErrorDecoratorService $errorDecorator
     * @return JsonResponse
     */
    public function createPeriod(
        Request $request,
        PeriodService $periodService,
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

        $result = $periodService->createPeriod($data);
        if ($result instanceof Period) {
            $status = JsonResponse::HTTP_CREATED;
            $data = [
                'data' => [
                    'id' => $result->getId(),
                    'name' => $result->getName(),
                    'name' => $result->getIntervalSpec()
                ]
            ];
        } else {
            $status = JsonResponse::HTTP_BAD_REQUEST;
            $data = $errorDecorator->decorateError($status, $result);
        }

        return new JsonResponse($data, $status);
    }

    /**
     * Update period by passed JSON data
     *
     * @Route("/api/periods/{id}")
     * @Method("PUT")
     * @param Period $period
     * @param Request $request
     * @param PeriodService $periodService
     * @param ResponseErrorDecoratorService $errorDecorator
     * @return JsonResponse
     */
    public function updatePeriod(
        Period $period,
        Request $request,
        PeriodService $periodService,
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

        $result = $periodService->updatePeriod($period, $data);
        if ($result instanceof Period) {
            $status = JsonResponse::HTTP_OK;
            $data = [
                'data' => [
                    'id' => $result->getId(),
                    'name' => $result->getName(),
                    'intervalSpec' => $period->getIntervalSpec()
                ]
            ];
        } else {
            $status = JsonResponse::HTTP_BAD_REQUEST;
            $data = $errorDecorator->decorateError($status, $result);
        }

        return new JsonResponse($data, $status);
    }

    /**
     * @Route("/api/periods/{id}")
     * @Method("DELETE")
     * @param Period $period
     * @param PeriodService $periodService
     * @param ResponseErrorDecoratorService $errorDecorator
     * @return JsonResponse
     */
    public function deletePeriod(
        Period $period,
        PeriodService $periodService,
        ResponseErrorDecoratorService $errorDecorator
    )
    {
        $result = $periodService->deletePeriod($period);
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
     * @Route("/api/periods/{id}")
     * @Method("GET")
     * @param Period $period Symfony will find period entity by {id} and will assign it to $period
     * @return JsonResponse Data array which contains information about period
     */
    public function getPeriod(Period $period)
    {
        $status = JsonResponse::HTTP_OK;
        $data = [
            'data' => [
                'period' => [
                    'id' => $period->getId(),
                    'name' => $period->getName(),
                    'intervalSpec' => $period->getIntervalSpec()
                ]
            ]
        ];

        return new JsonResponse($data, $status);
    }

    /**
     * @Route("/api/periods")
     * @Method("GET")
     * @param PeriodService $periodService
     * @return JsonResponse List of periods
     */
    public function getPeriods(PeriodService $periodService)
    {
        $periods = $periodService->getPeriods();
        $periodsArr = [];
        foreach ($periods as $period) {
            $periodsArr[] = [
                'id' => $period->getId(),
                'name' => $period->getName(),
                'intervalSpec' => $period->getIntervalSpec()
            ];
        }

        $status = JsonResponse::HTTP_OK;
        $data = [
            'data' => [
                'periods' => $periodsArr
            ]
        ];

        return new JsonResponse($data, $status);
    }
}