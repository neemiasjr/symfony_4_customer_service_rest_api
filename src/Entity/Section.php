<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\SectionRepository")
 * @ORM\Table(name="section")
 */
class Section
{
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Listing", mappedBy="section")
     * @ORM\OrderBy({"name"="ASC"})
     */
    private $listings;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true, length=191)
     */
    private $name;

    public function __construct()
    {
        // php-array on steroids
        $this->listings = new ArrayCollection();
    }

    /**
     * @return ArrayCollection|Listing[]
     */
    public function getListings()
    {
        return $this->listings;
    }

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

}