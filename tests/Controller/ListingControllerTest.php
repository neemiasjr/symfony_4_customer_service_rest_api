<?php

namespace App\Tests\Controller;

use App\Entity\Listing;
use App\Repository\ListingRepository;
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
        $description = "Test listing 1 description Test listing 1 description";
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

        $response = $this->client->post("listings", [
            'body' => json_encode($data)
        ]);

        $responseData = json_decode($response->getBody(), true);

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());
        $responseData = json_decode($response->getBody(), true);

        $this->assertArrayHasKey("data", $responseData);
        $this->assertArrayHasKey("id", $responseData['data']);
        $this->assertArrayHasKey("section_id", $responseData['data']);
        $this->assertArrayHasKey("title", $responseData['data']);
        $this->assertArrayHasKey("zip_code", $responseData['data']);
        $this->assertArrayHasKey("city_id", $responseData['data']);
        $this->assertArrayHasKey("description", $responseData['data']);
        $this->assertArrayHasKey("publication_date", $responseData['data']);
        $this->assertArrayHasKey("expiration_date", $responseData['data']);
        $this->assertArrayHasKey("user_id", $responseData['data']);

        // get just created listing
        $container = $this->getPrivateContainer();
        $listing = $container->get('doctrine')
            ->getRepository(Listing::class)
            ->find((int)$responseData['data']['id']);

        $this->assertEquals($listing->getId(), $responseData['data']['id']);
        $this->assertEquals($sectionId, $responseData['data']['section_id']);
        $this->assertEquals($title, $responseData['data']['title']);
        $this->assertEquals($zipCode, $responseData['data']['zip_code']);
        $this->assertEquals($cityId, $responseData['data']['city_id']);
        $this->assertEquals($description, $responseData['data']['description']);
        $this->assertEquals($listing->getPublicationDate()->format("Y-m-d H:i:s"), $responseData['data']['publication_date']);
        $this->assertEquals($listing->getExpirationDate()->format("Y-m-d H:i:s"), $responseData['data']['expiration_date']);
        $this->assertEquals($userId, $responseData['data']['user_id']);
    }

    public function testCreateListing____when_Creating_New_Listing_With_Invalid_Section____Listing_Is_NOT_Created_And_Error_Response_Is_Returned()
    {
        $test = $this->createTestListingWithData();
        $listing = $test['listing'];
        $data = $test['data'];

        $data['section_id'] = -1;

        $response = $this->client->post("listings", [
            'body' => json_encode($data),
        ]);
        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());

        $responseData = json_decode($response->getBody(), true);
        $this->assertArrayHasKey("error", $responseData);
        $this->assertArrayHasKey("code", $responseData['error']);
        $this->assertArrayHasKey("message", $responseData['error']);
        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $responseData['error']['code']);
        $errorMsg = "Unable to find section by given section_id";
        $this->assertEquals($errorMsg, $responseData['error']['message']);
    }

    public function testCreateListing____when_Creating_New_Listing_With_Invalid_Period____Listing_Is_NOT_Created_And_Error_Response_Is_Returned()
    {
        $test = $this->createTestListingWithData();
        $listing = $test['listing'];
        $data = $test['data'];

        $data['period_id'] = -1;

        $response = $this->client->post("listings", [
            'body' => json_encode($data),
        ]);
        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());

        $responseData = json_decode($response->getBody(), true);
        $this->assertArrayHasKey("error", $responseData);
        $this->assertArrayHasKey("code", $responseData['error']);
        $this->assertArrayHasKey("message", $responseData['error']);
        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $responseData['error']['code']);
        $errorMsg = "Unable to find period by given period_id";
        $this->assertEquals($errorMsg, $responseData['error']['message']);
    }

    public function testCreateListing____when_Creating_New_Listing_With_Invalid_City____Listing_Is_NOT_Created_And_Error_Response_Is_Returned()
    {
        $test = $this->createTestListingWithData();
        $listing = $test['listing'];
        $data = $test['data'];

        $data['city_id'] = -1;

        $response = $this->client->post("listings", [
            'body' => json_encode($data),
        ]);
        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());

        $responseData = json_decode($response->getBody(), true);
        $this->assertArrayHasKey("error", $responseData);
        $this->assertArrayHasKey("code", $responseData['error']);
        $this->assertArrayHasKey("message", $responseData['error']);
        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $responseData['error']['code']);
        $errorMsg = "Unable to find city by given city_id";
        $this->assertEquals($errorMsg, $responseData['error']['message']);
    }

    public function testCreateListing____when_Creating_New_Listing_With_Invalid_User____Listing_Is_NOT_Created_And_Error_Response_Is_Returned()
    {
        $test = $this->createTestListingWithData();
        $listing = $test['listing'];
        $data = $test['data'];

        $data['user_id'] = 'incorrectemail.com';

        $response = $this->client->post("listings", [
            'body' => json_encode($data),
        ]);
        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());

        $responseData = json_decode($response->getBody(), true);
        $this->assertArrayHasKey("error", $responseData);
        $this->assertArrayHasKey("code", $responseData['error']);
        $this->assertArrayHasKey("message", $responseData['error']);
        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $responseData['error']['code']);
        $errorMsg = "Unexpected user_id";
        $this->assertEquals($errorMsg, $responseData['error']['message']);
    }

    public function testCreateListing____when_Creating_New_Listing_With_Invalid_Data____Listing_Is_NOT_Created_And_Error_Response_Is_Returned()
    {
        $section = $this->createTestSection('Section 1');
        $sectionId = $section->getId();

        $title = "shrt";
        $zipCode = "Not German ZIPCODE";
        $cityId = $this->createTestCity('City 1')->getId();
        $description = "Too short";
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

        $response = $this->client->post("listings", [
            'body' => json_encode($data),
        ]);
        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());

        $responseData = json_decode($response->getBody(), true);
        $this->assertArrayHasKey("error", $responseData);
        $this->assertArrayHasKey("code", $responseData['error']);
        $this->assertArrayHasKey("message", $responseData['error']);
        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $responseData['error']['code']);
        $errorMsg = "Zip-code \"Not German ZIPCODE\" is invalid: it can only be a German zip-code."
            . "###Title must be at least 5 characters long"
            . "###Description must be at least 50 characters long";
        $this->assertEquals($errorMsg, $responseData['error']['message']);
    }

    public function testGetListing____when_Getting_Existing_Listing_With_Correct_Data____Listing_Is_Returned_With_Correct_Response_Status()
    {
        $test = $this->createTestListingWithData();
        $listing = $test['listing'];

        $response = $this->client->get("listings/{$listing->getId()}", []);

        $responseData = json_decode($response->getBody(), true);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        $this->assertArrayHasKey("data", $responseData);
        $this->assertArrayHasKey("id", $responseData['data']);
        $this->assertArrayHasKey("section_id", $responseData['data']);
        $this->assertArrayHasKey("title", $responseData['data']);
        $this->assertArrayHasKey("zip_code", $responseData['data']);
        $this->assertArrayHasKey("city_id", $responseData['data']);
        $this->assertArrayHasKey("description", $responseData['data']);
        $this->assertArrayHasKey("publication_date", $responseData['data']);
        $this->assertArrayHasKey("expiration_date", $responseData['data']);
        $this->assertArrayHasKey("user_id", $responseData['data']);

        // get just created listing
        $container = $this->getPrivateContainer();
        $listing = $container->get('doctrine')
            ->getRepository(Listing::class)
            ->find((int)$responseData['data']['id']);

        $this->assertEquals($listing->getId(), $responseData['data']['id']);
        $this->assertEquals($listing->getSection()->getId(), $responseData['data']['section_id']);
        $this->assertEquals($listing->getTitle(), $responseData['data']['title']);
        $this->assertEquals($listing->getZipCode(), $responseData['data']['zip_code']);
        $this->assertEquals($listing->getCity()->getId(), $responseData['data']['city_id']);
        $this->assertEquals($listing->getDescription(), $responseData['data']['description']);
        $this->assertEquals($listing->getPublicationDate()->format("Y-m-d H:i:s"), $responseData['data']['publication_date']);
        $this->assertEquals($listing->getExpirationDate()->format("Y-m-d H:i:s"), $responseData['data']['expiration_date']);
        $this->assertEquals($listing->getUser()->getEmail(), $responseData['data']['user_id']);
    }

    public function testGetListings____when_Getting_Existing_Listings_Having_Filter_Values____Success_Response_Is_Returned_With_Data()
    {
        $section = $this->createTestSection("Section 1");
        $city = $this->createTestCity("City 1");
        $period = $this->createTestPeriod("90 days", "P90D");
        $user = $this->createTestUser("test2@restapier.com", "test1234");

        $test = $this->createTestListingWithData([
            'title' => 'Listing 1',
            'section' => $section,
            'city' => $city,
            'period' => $period,
            'user' => $user
        ]);

        $data = $test['data'];

        $filter = [
            'section_id' => $data['section_id'],
            'city_id' => $data['city_id'],
            'days_back' => 10,
            'excluded_user_id' => "test2@restapier.com"
        ];

        $response = $this->client->get(
            "listings"
            . "?section_id={$filter['section_id']}"
            . "&city_id={$filter['city_id']}"
            . "&days_back={$filter['days_back']}"
            . "&excluded_user_id={$filter['excluded_user_id']}"
        );

        $responseData = json_decode($response->getBody(), true);


        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $responseData = json_decode($response->getBody(), true);
    }

    public function testUpdateListing____when_Updating_Listing_With_Correct_Data____Listing_Is_Updated_And_Returned_With_Correct_Response_Status()
    {
        $test = $this->createTestListingWithData();
        $listing = $test['listing'];

        $newListingTitle = "New Listing Title";
        $newZipCode = '21521';
        $newDescription = "Description is updated. Description is updated. Description is updated.";
        $newSection = $this->createTestSection('New section');
        $newCity = $this->createTestCity('New city');
        $newPeriod = $this->createTestPeriod('Period 90 days', 'P90D');

        $data = [
            'section_id' => $newSection->getId(),
            'title' => $newListingTitle,
            'zip_code' => $newZipCode,
            'city_id' => $newCity->getId(),
            'description' => $newDescription,
            'period_id' => $newPeriod->getId()
        ];

        $response = $this->client->put("listings/{$listing->getId()}", [
            'body' => json_encode($data),
        ]);

        $responseData = json_decode($response->getBody(), true);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        $this->assertArrayHasKey("data", $responseData);
        $this->assertArrayHasKey("id", $responseData['data']);
        $this->assertArrayHasKey("section_id", $responseData['data']);
        $this->assertArrayHasKey("title", $responseData['data']);
        $this->assertArrayHasKey("zip_code", $responseData['data']);
        $this->assertArrayHasKey("city_id", $responseData['data']);
        $this->assertArrayHasKey("description", $responseData['data']);
        $this->assertArrayHasKey("publication_date", $responseData['data']);
        $this->assertArrayHasKey("expiration_date", $responseData['data']);
        $this->assertArrayHasKey("user_id", $responseData['data']);

        // get just created listing
        $container = $this->getPrivateContainer();
        $listing = $container->get('doctrine')
            ->getRepository(Listing::class)
            ->find((int)$responseData['data']['id']);

        $this->assertEquals($listing->getId(), $responseData['data']['id']);
        $this->assertEquals($newSection->getId(), $responseData['data']['section_id']);
        $this->assertEquals($newListingTitle, $responseData['data']['title']);
        $this->assertEquals($newZipCode, $responseData['data']['zip_code']);
        $this->assertEquals($newCity->getId(), $responseData['data']['city_id']);
        $this->assertEquals($newDescription, $responseData['data']['description']);
        $this->assertEquals($listing->getPublicationDate()->format("Y-m-d H:i:s"), $responseData['data']['publication_date']);
        $this->assertEquals($listing->getExpirationDate()->format("Y-m-d H:i:s"), $responseData['data']['expiration_date']);
        $this->assertEquals($listing->getUser()->getEmail(), $responseData['data']['user_id']);
    }

    public function testUpdateListing____when_Updating_Listing_With_Title_Field_Only____Listing_Is_Updated_And_Success_Response_Is_Returned()
    {
        $test = $this->createTestListingWithData();
        $listing = $test['listing'];

        $newListingTitle = "New Listing Title";

        $data = [
            'title' => $newListingTitle
        ];

        $response = $this->client->put("listings/{$listing->getId()}", [
            'body' => json_encode($data),
        ]);

        $responseData = json_decode($response->getBody(), true);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        $this->assertArrayHasKey("data", $responseData);
        $this->assertArrayHasKey("id", $responseData['data']);
        $this->assertArrayHasKey("section_id", $responseData['data']);
        $this->assertArrayHasKey("title", $responseData['data']);
        $this->assertArrayHasKey("zip_code", $responseData['data']);
        $this->assertArrayHasKey("city_id", $responseData['data']);
        $this->assertArrayHasKey("description", $responseData['data']);
        $this->assertArrayHasKey("publication_date", $responseData['data']);
        $this->assertArrayHasKey("expiration_date", $responseData['data']);
        $this->assertArrayHasKey("user_id", $responseData['data']);

        // get just created listing
        $container = $this->getPrivateContainer();
        $listing = $container->get('doctrine')
            ->getRepository(Listing::class)
            ->find((int)$responseData['data']['id']);

        $this->assertEquals($listing->getId(), $responseData['data']['id']);
        $this->assertEquals($listing->getSection()->getId(), $responseData['data']['section_id']);
        $this->assertEquals($newListingTitle, $responseData['data']['title']);
        $this->assertEquals($listing->getZipCode(), $responseData['data']['zip_code']);
        $this->assertEquals($listing->getCity()->getId(), $responseData['data']['city_id']);
        $this->assertEquals($listing->getDescription(), $responseData['data']['description']);
        $this->assertEquals($listing->getPublicationDate()->format("Y-m-d H:i:s"), $responseData['data']['publication_date']);
        $this->assertEquals($listing->getExpirationDate()->format("Y-m-d H:i:s"), $responseData['data']['expiration_date']);
        $this->assertEquals($listing->getUser()->getEmail(), $responseData['data']['user_id']);
    }

    public function testUpdateListing____when_Updating_Listing_With_Invalid_JSON____Listing_Is_NOT_Updated_And_Error_Response_Is_Returned()
    {
        $test = $this->createTestListingWithData();
        $listing = $test['listing'];
        $response = $this->client->put("listings/{$listing->getId()}", [
            'body' => '{"notvalid"}'
        ]);
        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());

        $responseData = json_decode($response->getBody(), true);
        $this->assertArrayHasKey("error", $responseData);
        $this->assertArrayHasKey("code", $responseData['error']);
        $this->assertArrayHasKey("message", $responseData['error']);
        $this->assertEquals(400, $responseData['error']['code']);
        $this->assertEquals("Invalid JSON format", $responseData['error']['message']);
    }

    public function testUpdateListing____when_Updating_Nonexistent_Listing____Error_Response_Is_Returned()
    {
        $test = $this->createTestListingWithData();
        $listing = $test['listing'];

        $response = $this->client->put("listings/7777777", []);
        $this->assertEquals(JsonResponse::HTTP_NOT_FOUND, $response->getStatusCode());

        $responseData = json_decode($response->getBody(), true);

        $this->assertArrayHasKey("error", $responseData);
        $this->assertArrayHasKey("code", $responseData['error']);
        $this->assertArrayHasKey("message", $responseData['error']);
        $this->assertEquals(JsonResponse::HTTP_NOT_FOUND, $responseData['error']['code']);
        $this->assertEquals("Not Found", $responseData['error']['message']);
    }

    public function testDeleteListing____when_Deleting_Existing_Listing____Listing_Is_Deleted_And_Status_204_Is_Returned()
    {
        $test = $this->createTestListingWithData();
        $listing = $test['listing'];

        $response = $this->client->delete("listings/{$listing->getId()}", []);
        $this->assertEquals(JsonResponse::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}