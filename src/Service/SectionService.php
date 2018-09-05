<?php

namespace App\Service;

use App\Entity\Section;
use Doctrine\ORM\EntityManagerInterface;

class SectionService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

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