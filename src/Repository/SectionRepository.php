<?php

namespace App\Repository;

use App\Entity\Section;
use Doctrine\ORM\EntityRepository;

class SectionRepository extends EntityRepository
{
    /**
     * @return Section[]
     */
    public function findAllOrderedByName(): array
    {
        $qb = $this->createQueryBuilder('s')
            ->orderBy('s.name', 'ASC')
            ->getQuery();

        return $qb->execute();
    }
}