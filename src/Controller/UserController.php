<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\ResponseErrorDecoratorService;
use App\Service\UserService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * Creates new user by given data
     *
     * @Route("/users/create", methods={"POST"})
     * @param Request $request
     * @param UserService $userService
     * @param ResponseErrorDecoratorService $errorDecorator
     * @return JsonResponse
     */
    public function createUser(
        Request $request,
        UserService $userService,
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
        $result = $userService->createUser($data);
        if ($result instanceof User) {
            $status = JsonResponse::HTTP_CREATED;
            $data = [
                'data' => [
                    'email' => $result->getEmail()
                ]
            ];
        } else {
            $status = JsonResponse::HTTP_BAD_REQUEST;
            $data = $errorDecorator->decorateError($status, $result);
        }
        return new JsonResponse($data, $status);
    }
}