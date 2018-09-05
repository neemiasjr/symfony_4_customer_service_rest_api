<?php

namespace App\Service;

use App\Entity\City;
use Doctrine\ORM\EntityManagerInterface;

class CityService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Create city by given data
     *
     * @param $data array which contains information about city
     *    $data = [
     *      'name' => (string) City name. Required.
     *    ]
     * @return City|string City or error message
     */
    public function createCity(array $data)
    {
        $cityName = $data['name'];
        if (empty($cityName)) {
            return "City name must not be empty!";
        }
        try {
            $city = new City();
            $city->setName($cityName);
            $this->em->persist($city);
            $this->em->flush();

            return $city;
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex) {
            return "City with given name already exists";
        } catch (\Exception $ex) {
            return "Unable to create city";
        }
    }

    /**
     * @param City $city
     * @return bool|string True if city was successfully deleted, error message otherwise
     */
    public function deleteCity(City $city)
    {
        try {
            $this->em->remove($city);
            $this->em->flush();
        } catch (\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException $ex) {
            return "Can't delete city. There are listings assigned to it. Remove them first!";
        } catch (\Exception $ex) {
            return "Unable to remove city";
        }

        return true;
    }

    /**
     * Get all cities
     *
     * @return City[]
     */
    public function getCities(): array
    {
        $repository = $this->em->getRepository(City::class);
        $cities = $repository->findAllOrderedByName();

        return $cities;
    }
}