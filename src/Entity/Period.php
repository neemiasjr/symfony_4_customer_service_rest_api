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
     * @ORM\Column(type="string", unique=true, length=191)
     */
    private $name;

    /**
     * @ORM\Column(name="date_addon", type="string", length=100)
     */
    private $intervalSpec;

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
    public function getIntervalSpec()
    {
        return $this->intervalSpec;
    }

    /**
     * @param string $intervalSpec
     * Please set interval_spec according to format specified in PHP manual:
     *  http://www.php.net/manual/en/dateinterval.construct.php
     *
     * Example: 'P3D' which means Period = 3 days
     */
    public function setIntervalSpec($intervalSpec): void
    {
        $this->intervalSpec = $intervalSpec;
    }
}