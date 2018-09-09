<?php


namespace App\Controller;

use App\Entity\Listing;
use App\Service\ListingService;
use App\Service\ResponseErrorDecoratorService;
use App\Service\SectionService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ListingController extends Controller
{
    /**
     * Creates new listing by passed JSON data
     *
     * @Route("/api/listings", methods={"POST"})
     * @param Request $request
     * @param ListingService $listingService
     * @param ResponseErrorDecoratorService $errorDecorator
     * @return JsonResponse
     */
    public function createListing(
        Request $request,
        ListingService $listingService,
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

        $result = $listingService->createListing($data);

        if ($result instanceof Listing) {
            $status = JsonResponse::HTTP_CREATED;
            $data = [
                'data' => [
                    'id' => $result->getId(),
                    'section_id' => $result->getSection()->getId(),
                    'title' => $result->getTitle(),
                    'zip_code' => $result->getZipCode(),
                    'city_id' => $result->getCity()->getId(),
                    'description' => $result->getDescription(),
                    'publication_date' => $result->getPublicationDate()->format("Y-m-d H:i:s"),
                    'expiration_date' => $result->getExpirationDate()->format("Y-m-d H:i:s"),
                    'user_id' => $result->getUser()->getEmail(),
                ]
            ];
        } else {
            $status = JsonResponse::HTTP_BAD_REQUEST;
            $data = $errorDecorator->decorateError($status, $result);
        }

        return new JsonResponse($data, $status);
    }

    /**
     * @Route("/api/listings/{id}", methods={"GET"})
     * @param Listing $listing Symfony will find listing entity by {id} and will assign it to $listing
     * @return JsonResponse Data array which contains information about listing
     */
    public function getListing(Listing $listing)
    {
        $status = JsonResponse::HTTP_OK;
        $data = [
            'data' => [
                'id' => $listing->getId(),
                'section_id' => $listing->getSection()->getId(),
                'title' => $listing->getTitle(),
                'zip_code' => $listing->getZipCode(),
                'city_id' => $listing->getCity()->getId(),
                'description' => $listing->getDescription(),
                'publication_date' => $listing->getPublicationDate()->format("Y-m-d H:i:s"),
                'expiration_date' => $listing->getExpirationDate()->format("Y-m-d H:i:s"),
                'user_id' => $listing->getUser()->getEmail(),
            ]
        ];

        return new JsonResponse($data, $status);
    }

    /**
     * @Route("/api/listings", methods={"GET"})
     * @param ListingService $listingService
     * @param array $filter
     * @return JsonResponse List of listings
     */
    public function getListings(ListingService $listingService, array $filter)
    {
        $listings = $listingService->getListings($filter);
        $listingsArr = [];
        foreach ($listings as $listing) {
            $listingsArr[] = [
                'id' => $listing->getId(),
                'section_id' => $listing->getSection()->getId(),
                'title' => $listing->getTitle(),
                'zip_code' => $listing->getZipCode(),
                'city_id' => $listing->getCity()->getId(),
                'description' => $listing->getDescription(),
                'publication_date' => $listing->getPublicationDate()->format("Y-m-d H:i:s"),
                'expiration_date' => $listing->getExpirationDate()->format("Y-m-d H:i:s"),
                'user_id' => $listing->getUser()->getEmail(),
            ];
        }

        $status = JsonResponse::HTTP_OK;
        $data = [
            'data' => [
                'listings' => $listingsArr
            ]
        ];

        return new JsonResponse($data, $status);
    }

    /**
     * Update listing by passed JSON data
     *
     * @Route("/api/listings/{id}", methods={"PUT"})
     * @param Listing $listing
     * @param Request $request
     * @param ListingService $listingService
     * @param ResponseErrorDecoratorService $errorDecorator
     * @return JsonResponse
     */
    public function updateListing(
        Listing $listing,
        Request $request,
        ListingService $listingService,
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

        $result = $listingService->updateListing($listing, $data);

        if ($result instanceof Listing) {
            $status = JsonResponse::HTTP_OK;
            $data = [
                'data' => [
                    'id' => $result->getId(),
                    'section_id' => $result->getSection()->getId(),
                    'title' => $result->getTitle(),
                    'zip_code' => $result->getZipCode(),
                    'city_id' => $result->getCity()->getId(),
                    'description' => $result->getDescription(),
                    'publication_date' => $result->getPublicationDate()->format("Y-m-d H:i:s"),
                    'expiration_date' => $result->getExpirationDate()->format("Y-m-d H:i:s"),
                    'user_id' => $result->getUser()->getEmail(),
                ]
            ];
        } else {
            $status = JsonResponse::HTTP_BAD_REQUEST;
            $data = $errorDecorator->decorateError($status, $result);
        }

        return new JsonResponse($data, $status);
    }

    /**
     * @Route("/api/listings/{id}", methods={"DELETE"})
     * @param Listing $listing
     * @param ListingService $listingService
     * @param ResponseErrorDecoratorService $errorDecorator
     * @return JsonResponse
     */
    public function deleteListing(
        Listing $listing,
        ListingService $listingService,
        ResponseErrorDecoratorService $errorDecorator
    )
    {
        $result = $listingService->deleteListing($listing);
        if ($result === true) {
            $status = JsonResponse::HTTP_NO_CONTENT;
            $data = null;
        } else {
            $status = JsonResponse::HTTP_BAD_REQUEST;
            $data = $errorDecorator->decorateError($status, $result);
        }

        return new JsonResponse($data, $status);
    }
}