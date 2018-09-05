<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Listing")
 * @ORM\Table(name="listing")
 */
class Listing
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="listings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Section", inversedBy="listings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $section;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\City", inversedBy="listings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $city;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(name="zip_code", type="string")
     */
    private $zipCode;
    /**
     * @ORM\Column(type="string")
     */
    private $title;
    /**
     * @ORM\Column(type="text")
     */
    private $description;
    /**
     * @ORM\Column(name="publication_date", type="datetime", options={"default": 0})
     */
    private $publicationDate;
    /**
     * @ORM\Column(name="expiration_date", type="datetime")
     */
    private $expirationDate;

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * @param mixed $section
     */
    public function setSection($section): void
    {
        $this->section = $section;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city): void
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * @param mixed $zipCode
     */
    public function setZipCode($zipCode): void
    {
        $this->zipCode = $zipCode;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * @param mixed $expirationDate
     */
    public function setExpirationDate($expirationDate): void
    {
        $this->expirationDate = $expirationDate;
    }


}