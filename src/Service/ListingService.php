<?php


namespace App\Service;

use App\Entity\Listing;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

class ListingService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Create listing by given data
     *
     * @param $data array which contains information about listing
     *    $data = [
     *      'section_id' => (int) Section id. Required.
     *      'title' => (string) Title. Required.
     *      'zip_code' => (string) Zip-code. Required.
     *      'city_id' => (int) City id. Required.
     *      'description' => (string) Description. Required.
     *      'period_id' => (int) Period id. Required.
     *      'user_id' => (int) User id. Required.
     *    ]
     * @return Listing|string Listing or error message
     */
    public function createListing($data)
    {
        $violations = $this->getViolations($data);

        try {

            $user = $this->em
                ->getRepository(User::class)
                ->find((int)$data['user_id']);
            if (!$user) {
                return "Unable to find user by given user_id";
            }

            $section = $this->em
                ->getRepository(Section::class)
                ->find((int)$data['section_id']);
            if (!$section) {
                return "Unable to find section by given section_id";
            }

            $city = $this->em
                ->getRepository(City::class)
                ->find((int)$data['city_id']);
            if (!$city) {
                return "Unable to find city by given city_id";
            }

            $period = $this->em
                ->getRepository(Period::class)
                ->find((int)$data['period_id']);
            if (!$period) {
                return "Unable to find period by given period_id";
            }

            $listing = new Listing();
            $listing->setCity($city);
            $listing->setSection($section);
            $listing->setUser($user);

            $listing->setName($data['name']);
            $listing->setStrip($data['strip']);
            $listing->setLeague($league);

            $this->em->persist($listing);
            $this->em->flush();

            return $listing;

        } catch (\Exception $ex) {
            return "Unable to create listing";
        }
    }

    /**
     * Validate listing data and get violations (if any)
     *
     * @param $data array which contains information about listing
     *    $data = [
     *      'section_id' => (int) Section id. Required.
     *      'title' => (string) Title. Required.
     *      'zip_code' => (string) Zip-code. Required.
     *      'city_id' => (int) City id. Required.
     *      'description' => (string) Description. Required.
     *      'period_id' => (int) Period id. Required.
     *      'user_id' => (int) User id. Required.
     *    ]
     * @return ConstraintViolationList
     */
    public function getViolations($data)
    {
        $validator = Validation::createValidator();

        $zipCodeLength = 5;
        $constraint = new Assert\Collection(array(
            'section_id' => new Assert\Type(array('type' => 'integer', 'message' => 'Unexpected section_id')),
            'title' => new Assert\Length(array('min' => 5, 'max' => 50)),
            'zip_code' => new Assert\Regex(array(
                "pattern" => "/[0-9]{" . $zipCodeLength . "}/",
                "message" => "Zip code must consist of exactly {$zipCodeLength} numbers from 0 to 9"
            )),
            'city_id' => new Assert\Type(array('type' => 'integer', 'message' => 'Unexpected city_id')),
            'title' => new Assert\Length(array('min' => 5, 'max' => 500)),
            'period_id' => new Assert\Type(array('type' => 'integer', 'message' => 'Unexpected period_id')),
            'user_id' => new Assert\Type(array('type' => 'integer', 'message' => 'Unexpected user_id')),
        ));

        $violations = $validator->validate($data, $constraint);

        return $violations;
    }
}