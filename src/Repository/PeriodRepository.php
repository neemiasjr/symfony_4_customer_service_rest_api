<?php

namespace App\Repository;

use App\Entity\Period;
use Doctrine\ORM\EntityRepository;

class PeriodRepository extends EntityRepository
{
    /**
     * @return Period[]
     */
    public function findAllOrderedByName(): array
    {
        $qb = $this->createQueryBuilder('p')
            ->orderBy('p.name', 'ASC')
            ->getQuery();

        return $qb->execute();
    }
}