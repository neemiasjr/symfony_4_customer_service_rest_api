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
     * @return Listing[] Array of Listing objects
     */
    public function findAllFiltered(array $filter): array
    {
        $qb = $this->createQueryBuilder('l')
            ->orderBy('l.publicationDate', 'ASC');

        if (sizeof($filter)) {
            if (isset($filter['days_back'])) {

                $date = date('Y-m-d', strtotime("-{$filter['days_back']} days"));

                $qb->andWhere('l.publicationDate >= :back_then')
                    ->setParameter('back_then', $date);
            }
            if (isset($filter['city_id'])) {
                $qb->andWhere('IDENTITY(l.city) = :city_id')
                    ->setParameter('city_id', $filter['city_id']);
            }
            if (isset($filter['section_id'])) {
                $qb->andWhere('IDENTITY(l.section) = :section_id')
                    ->setParameter('section_id', $filter['section_id']);
            }
            if (isset($filter['excluded_user_id'])) {
                $qb->andWhere('IDENTITY(l.user) = :excluded_user_id')
                    ->setParameter('excluded_user_id', $filter['excluded_user_id']);
            }
        }

        $qb->andWhere('l.expirationDate >= :today')
            ->setParameter('today', date('Y-m-d H:i:s'));

        return $qb->getQuery()->execute();
    }
}