<?php

namespace App\Service;

use App\Entity\Period;
use Doctrine\ORM\EntityManagerInterface;

class PeriodService extends BaseService
{
    /**
     * Create period by given data
     *
     * @param $data array which contains information about period
     *    $data = [
     *      'name' => (string) Period name. Required.
     *      'interval_spec' => (string) Interval spec. Required.
     *    ]
     * @return Period|string Period or error message
     */
    public function createPeriod(array $data)
    {
        $periodName = $data['name'];
        if (empty($periodName)) {
            return "Period name must not be empty!";
        }
        $intervalSpec = $data['interval_spec'];
        if (empty($intervalSpec)) {
            return "Interval spec must not be empty!";
        }

        try {
            $period = new Period();
            $period->setName($periodName);
            $period->setIntervalSpec($intervalSpec);
            $this->em->persist($period);
            $this->em->flush();

            return $period;
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex) {
            return "Period with given name already exists";
        } catch (\Exception $ex) {
            return "Unable to create period";
        }
    }

    /**
     * @param Period $period
     * @return bool|string True if period was successfully deleted, error message otherwise
     */
    public function deletePeriod(Period $period)
    {
        try {
            $this->em->remove($period);
            $this->em->flush();
        } catch (\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException $ex) {
            return "Can't delete period. There are listings assigned to it. Remove them first!";
        } catch (\Exception $ex) {
            return "Unable to remove period";
        }

        return true;
    }

    /**
     * Get all periods
     *
     * @return Period[]
     */
    public function getPeriods(): array
    {
        $repository = $this->em->getRepository(Period::class);
        $periods = $repository->findAllOrderedByName();

        return $periods;
    }
}