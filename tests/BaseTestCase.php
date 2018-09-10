<?php

namespace App\Tests;


use App\Entity\City;
use App\Entity\Listing;
use App\Entity\Period;
use App\Entity\Section;
use App\Entity\User;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BaseTestCase extends KernelTestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var User
     */
    protected $testUser;

    /**
     * setUp() method executes before every test
     */
    public function setUp()
    {
        $container = $this->getPrivateContainer();
        $sectionService = $container
            ->get('App\Service\SectionService');

        $container = $this->getPrivateContainer();
        $cityService = $container
            ->get('App\Service\CityService');

        $this->client = new Client([
            'base_uri' => 'http://cs.loc/api/',
            'exceptions' => false
        ]);

        $container = $this->getPrivateContainer();

        $this->em = $container
            ->get('doctrine')
            ->getManager();

        $this->truncateTables();
    }

    private function truncateTables()
    {
        $em = $this->em;

        $query = $em->createQuery('DELETE App:Listing l WHERE 1 = 1');
        $query->execute();

        $query = $em->createQuery('DELETE App:Section s WHERE 1 = 1');
        $query->execute();

        $query = $em->createQuery('DELETE App:User u WHERE 1 = 1');
        $query->execute();

        $query = $em->createQuery('DELETE App:City c WHERE 1 = 1');
        $query->execute();

        $query = $em->createQuery('DELETE App:Period p WHERE 1 = 1');
        $query->execute();

        parent::tearDown();

        $this->em->close();
        $this->em = null; // avoid memory leaks
    }

    /**
     * @param $name
     * @return Section
     */
    protected function createTestSection($name)
    {
        $container = $this->getPrivateContainer();
        $sectionService = $container
            ->get('App\Service\SectionService');
        return $sectionService->createSection([
            'name' => $name
        ]);
    }

    /**
     * @param $name
     * @return City
     */
    protected function createTestCity($name)
    {
        $container = $this->getPrivateContainer();
        $cityService = $container
            ->get('App\Service\CityService');
        return $cityService->createCity([
            'name' => $name
        ]);
    }

    /**
     * @param $name
     * @param $intervalSpec
     * @return Period
     */
    protected function createTestPeriod($name, $intervalSpec)
    {
        $container = $this->getPrivateContainer();
        $periodService = $container
            ->get('App\Service\PeriodService');
        return $periodService->createPeriod([
            'name' => $name,
            'interval_spec' => 'P60D'
        ]);
    }

    /**
     * @param $email
     * @param $password
     * @return User
     */
    protected function createTestUser($email, $password)
    {
        $container = $this->getPrivateContainer();
        $userService = $container
            ->get('App\Service\UserService');
        return $userService->createUser([
            'email' => $email,
            'password' => $password
        ]);
    }

    /**
     * @return array which contains Listing and data array
     */
    protected function createTestListingWithData($params = []): array
    {
        $section = isset($params['section']) ? $params['section'] : $this->createTestSection('Section 1');
        $sectionId = $section->getId();

        $title = isset($params['title']) ? $params['title'] : "Test listing 1";
        $zipCode = "10115";
        $city = isset($params['city']) ? $params['city'] : $this->createTestCity('City 1');
        $cityId = $city->getId();
        $description = "Test listing 1 description Test listing 1 description";
        $period = isset($params['period']) ? $params['period'] : $this->createTestPeriod("Plus 60 days", "P60D");
        $periodId = $period->getId();
        $user = isset($params['user']) ? $params['user'] : $this->createTestUser("test1@restapier.com", "pass1");
        $userId = $user->getEmail();

        $data = [
            'section_id' => $sectionId,
            'title' => $title,
            'zip_code' => $zipCode,
            'city_id' => $cityId,
            'description' => $description,
            'period_id' => $periodId,
            'user_id' => $userId
        ];

        $container = $this->getPrivateContainer();
        $listingService = $container
            ->get('App\Service\ListingService');

        $listing = $listingService->createListing($data);

        return [
            'listing' => $listing,
            'data' => $data
        ];
    }

    protected function getPrivateContainer()
    {
        self::bootKernel();

        // returns the real and unchanged service container
        $container = self::$kernel->getContainer();

        // gets the special container that allows fetching private services
        $container = self::$container;

        return $container;
    }

    protected function tearDown()
    {
        parent::tearDown();
    }
}