<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @UniqueEntity("email")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="user")
 */
class User
{
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Listing", mappedBy="user")
     * @ORM\OrderBy({"publication_date"="ASC"})
     */
    private $listings;

    /**
     * @ORM\Id
     * @ORM\Column(type="string", name="id", unique=true, length=191)
     */
    private $email;
    /**
     * @ORM\Column(type="string")
     */
    private $password;

    public function __construct()
    {
        // php-array on steroids
        $this->listings = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password Hashed password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }

}