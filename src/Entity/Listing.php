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
}