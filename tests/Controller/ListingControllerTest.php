<?php

namespace App\Tests\Controller;

use App\Tests\BaseTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class ListingControllerTest extends BaseTestCase
{
    public function testCreateListing____when_Creating_New_Listing____Lising_Is_Created_And_Returned_With_Correct_Response_Status()
    {
        $section = $this->createTestSection('Section 1');
        $sectionId = $section->getId();

        $title = "Test listing 1";
        $zipCode = "10115";
        $cityId = $this->createTestCity('City 1')->getId();
        $description = "Test listing 1 description";
        $periodId = $this->createTestPeriod("Plus 60 days", "P60D")->getId();
        $userId = $this->createTestUser("test1@restapier.com", "pass1")->getEmail();

        $data = [
            'section_id' => $sectionId,
            'title' => $title,
            'zip_code' => $zipCode,
            'city_id' => $cityId,
            'description' => $description,
            'period_id' => $periodId,
            'user_id' => $userId
        ];

        print_r(json_encode($data));
        die();

        $response = $this->client->post("listings", [
            'body' => json_encode($data)
        ]);

        $responseData = json_decode($response->getBody(), true);

        print_r("aaaXXXXXXXXXX");
        print_r($responseData);
        die();

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());
        $responseData = json_decode($response->getBody(), true);

        $this->assertArrayHasKey("data", $responseData);
        $this->assertArrayHasKey("listing_id", $responseData['data']);
        $this->assertArrayHasKey("section_id", $responseData['data']);
        $this->assertArrayHasKey("title", $responseData['data']);
        $this->assertArrayHasKey("zip_code", $responseData['data']);
        $this->assertArrayHasKey("city_id", $responseData['data']);
        $this->assertArrayHasKey("description", $responseData['data']);
        $this->assertArrayHasKey("publication_date", $responseData['data']);
        $this->assertArrayHasKey("expiration_date", $responseData['data']);
        $this->assertArrayHasKey("user_id", $responseData['data']);

        // get just created listing
        $listings = $section->getListings();
        $this->assertTrue(sizeof($listings));
        $listing = $listings[0];

        $this->assertEquals($listing->getId(), $responseData['data']['listing_id']);
        $this->assertEquals($sectionId, $responseData['data']['section_id']);
        $this->assertEquals($title, $responseData['data']['title']);
        $this->assertEquals($zipCode, $responseData['data']['zip_code']);
        $this->assertEquals($cityId, $responseData['data']['city_id']);
        $this->assertEquals($description, $responseData['data']['description']);
        $this->assertEquals($listing > getPublicationDate()->format("yyyy-MM-dd HH:mm:ss.SSS"), $responseData['data']['publication_date']);
        $this->assertEquals($listing > getExpirationDate()->format("yyyy-MM-dd HH:mm:ss.SSS"), $responseData['data']['expiration_date']);
        $this->assertEquals($userId, $responseData['data']['user_id']);
    }
}