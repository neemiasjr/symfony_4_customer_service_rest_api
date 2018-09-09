<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class ListingRepository extends EntityRepository
{
    /**
     *
     * @param array $filter Array of filters to use during listings selection
     *
     *    Filter example (all keys are optional):
     *
     *    $filter = [
     *      'section_id' => (int) Section id. Optional.
     *      'city_id' => (int) City id. Optional.
     *      'days_back' => (int) Look for listings up to `days_back` days after initial creation. Optional.
     *      'excluded_user_id' => (int) Exclude listings for given user id. Optional.
     *    ]
     * @return Listing[]
     */
    public function findAllFiltered(array $filter): array
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
            if (isset($filter['excluded_user_id'])) {
                $qb->andWhere('l.user_id != :user_id')
                    ->setParameter('user_id', $filter['excluded_user_id']);
            }
        }

        $qb->getQuery();

        return $qb->execute();
    }
}