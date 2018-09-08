<?php

namespace App\Service;

use App\Entity\Section;

class SectionService extends BaseService
{
    /**
     * Create section by given data
     *
     * @param $data array which contains information about section
     *    $data = [
     *      'name' => (string) Section name. Required.
     *    ]
     * @return Section|string Section or error message
     */
    public function createSection(array $data)
    {
        $sectionName = $data['name'];
        if (empty($sectionName)) {
            return "Section name must not be empty!";
        }
        try {
            $section = new Section();
            $section->setName($sectionName);
            $this->em->persist($section);
            $this->em->flush();

            return $section;
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex) {
            return "Section with given name already exists";
        } catch (\Exception $ex) {
            return "Unable to create section";
        }
    }

    /**
     * Update section by given data
     *
     * @param Section $section Section to update
     * @param $data array which contains information about section
     *    $data = [
     *      'name' => (string) Title. Optional.
     *    ]
     * @return Section|string Section or error message
     */
    public function updateSection(Section $section, array $data)
    {
        $violations = $this->getUpdateSectionViolations($data);
        if (sizeof($violations)) {
            return $this->getErrorsStr($violations);
        }

        try {

            if (isset($data['section_id'])) {
                $section = $this->em
                    ->getRepository(Section::class)
                    ->find((int)$data['section_id']);
                if (!$section) {
                    return "Unable to find section by given section_id";
                }

                $listing->setSection($section);
            }

            $listing->setTitle($data['title']);
            $listing->setZipCode($data['zip_code']);

            if (isset($data['city_id'])) {
                $city = $this->em
                    ->getRepository(City::class)
                    ->find((int)$data['city_id']);
                if (!$city) {
                    return "Unable to find city by given city_id";
                }

                $listing->setCity($city);
            }

            $listing->setDescription($data['description']);

            if (isset($data['period_id'])) {
                $period = $this->em
                    ->getRepository(Period::class)
                    ->find((int)$data['period_id']);
                if (!$period) {
                    return "Unable to find period by given period_id";
                }

                $publicationDate = $listing->getPublicationDate();
                $expirationDate = $publicationDate->add(new \DateInterval($period->getIntervalSpec()));

                $listing->setExpirationDate($expirationDate);
            }

            $this->em->persist($listing);
            $this->em->flush();

            return $listing;

        } catch (\Exception $ex) {
            return "Unable to update listing";
        }
    }

    /**
     * @param Section $section
     * @return bool|string True if section was successfully deleted, error message otherwise
     */
    public function deleteSection(Section $section)
    {
        try {
            $this->em->remove($section);
            $this->em->flush();
        } catch (\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException $ex) {
            return "Can't delete section. There are listings assigned to it. Remove them first!";
        } catch (\Exception $ex) {
            return "Unable to remove section";
        }

        return true;
    }

    /**
     * Get all sections
     *
     * @return Section[]
     */
    public function getSections(): array
    {
        $repository = $this->em->getRepository(Section::class);
        $sections = $repository->findAllOrderedByName();

        return $sections;
    }
}