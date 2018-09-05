<?php

namespace App\Repository;

use App\Entity\City;
use Doctrine\ORM\EntityRepository;

class ListingRepository extends EntityRepository
{
    /**
     *
     * @param $filter Array of filters to use during listings selection
     * @return Listing[]
     */
    public function findAll($filter): array
    {
        $qb = $this->createQueryBuilder('l')
            ->orderBy('l.publicationDate', 'ASC')
            ->getQuery();

        return $qb->execute();
    }
}