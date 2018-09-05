<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="period")
 */
class Period
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $name;

    /**
     * @ORM\Column(name="date_addon", type="string")
     */
    private $dateAddon;

    /**
     * @return mixed
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDateAddon()
    {
        return $this->dateAddon;
    }

    /**
     * @param string $dateAddon
     */
    public function setDateAddon($dateAddon): void
    {
        $this->dateAddon = $dateAddon;
    }
}