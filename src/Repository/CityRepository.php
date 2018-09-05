<?php

namespace App\Repository;

use App\Entity\City;
use Doctrine\ORM\EntityRepository;

class CityRepository extends EntityRepository
{
    /**
     * @return City[]
     */
    public function findAllOrderedByName(): array
    {
        $qb = $this->createQueryBuilder('c')
            ->orderBy('c.name', 'ASC')
            ->getQuery();

        return $qb->execute();
    }
}