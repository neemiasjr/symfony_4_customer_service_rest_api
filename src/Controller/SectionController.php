<?php


namespace App\Controller;

use App\Entity\Section;
use App\Service\SectionService;
use App\Service\ResponseErrorDecoratorService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SectionController extends Controller
{
    /**
     * Creates new section by passed JSON data
     *
     * @Route("/api/sections")
     * @Method("POST")
     * @param Request $request
     * @param SectionService $sectionService
     * @param ResponseErrorDecoratorService $errorDecorator
     * @return JsonResponse
     */
    public function createSection(
        Request $request,
        SectionService $sectionService,
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

        $result = $sectionService->createSection($data);
        if ($result instanceof Section) {
            $status = JsonResponse::HTTP_CREATED;
            $data = [
                'data' => [
                    'id' => $result->getId(),
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
     * Update section by passed JSON data
     *
     * @Route("/api/sections/{id}")
     * @Method("PUT")
     * @param Section $section
     * @param Request $request
     * @param SectionService $sectionService
     * @param ResponseErrorDecoratorService $errorDecorator
     * @return JsonResponse
     */
    public function updateSection(
        Section $section,
        Request $request,
        SectionService $sectionService,
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

        $result = $sectionService->updateSection($section, $data);
        if ($result instanceof Section) {
            $status = JsonResponse::HTTP_OK;
            $data = [
                'data' => [
                    'section_id' => $result->getId(),
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
     * @Route("/api/sections/{id}")
     * @Method("DELETE")
     * @param Section $section
     * @param SectionService $sectionService
     * @param ResponseErrorDecoratorService $errorDecorator
     * @return JsonResponse
     */
    public function deleteSection(
        Section $section,
        SectionService $sectionService,
        ResponseErrorDecoratorService $errorDecorator
    )
    {
        $result = $sectionService->deleteSection($section);
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
     * @Route("/api/sections/{id}")
     * @Method("GET")
     * @param Section $section Symfony will find section entity by {id} and will assign it to $section
     * @return JsonResponse Data array which contains information about section
     */
    public function getSection(Section $section)
    {
        $status = JsonResponse::HTTP_OK;
        $data = [
            'data' => [
                'section' => [
                    'section_id' => $section->getId(),
                    'name' => $section->getName()
                ]
            ]
        ];

        return new JsonResponse($data, $status);
    }

    /**
     * @Route("/api/sections")
     * @Method("GET")
     * @param SectionService $sectionService
     * @return JsonResponse List of sections
     */
    public function getCities(SectionService $sectionService)
    {
        $sections = $sectionService->getCities();
        $sectionsArr = [];
        foreach ($sections as $section) {
            $sectionsArr[] = [
                'section_id' => $section->getId(),
                'name' => $section->getName(),
            ];
        }

        $status = JsonResponse::HTTP_OK;
        $data = [
            'data' => [
                'sections' => $sectionsArr
            ]
        ];

        return new JsonResponse($data, $status);
    }
}