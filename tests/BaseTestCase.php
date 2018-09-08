<?php

namespace App\Tests;


use App\Entity\User;
use App\Service\SectionService;
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

    private function getPrivateContainer()
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