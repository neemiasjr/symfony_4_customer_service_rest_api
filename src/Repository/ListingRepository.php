<?php

namespace App\Repository;

use App\Entity\City;
use Doctrine\ORM\EntityRepository;

class ListingRepository extends EntityRepository
{
    /**
     *
     * @param $filter Array of filters to use during listings selection
     *
     *    Filter example (all keys are optional):
     *
     *    $filter = [
     *      'section_id' => (int) Section id. Optional.
     *      'city_id' => (int) City id. Optional.
     *      'days_back' => (int) Look for listings up to `days_back` days after initial creation. Optional.
     *    ]
     * @return Listing[]
     */
    public function findAllFiltered($filter): array
    {
        $qb = $this->createQueryBuilder('l')
            ->orderBy('l.publicationDate', 'ASC');

        if (sizeof($filter)) {
            if (isset($filter['days_back'])) {

                $date = date('yyyy-MM-dd', strtotime("-{$filter['days_back']} days"));

                $qb->andWhere('l.publication_date BETWEEN :back_then AND :today')
                    ->setParameter('today', date('yyyy-MM-dd'))
                    ->setParameter('back_then', $date);
            }
            if (isset($filter['city_id'])) {
                $qb->andWhere('l.city_id = :city_id')
                    ->setParameter('city_id', $filter['city_id']);
            }
            if (isset($filter['section_id'])) {
                $qb->andWhere('l.section_id = :section_id')
                    ->setParameter('section_id', $filter['section_id']);
            }
        }

        $qb->getQuery();

        return $qb->execute();
    }
}