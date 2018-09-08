<?php


namespace App\Service;

use App\Entity\Listing;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as ListingAssert;

class ListingService extends BaseService
{
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
    public function createListing(array $data)
    {
        $violations = $this->getCreateListingViolations($data);
        if (sizeof($violations)) {
            return $this->getErrorsStr($violations);
        }

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
            $listing->setSection($section);
            $listing->setTitle($data['title']);
            $listing->setZipCode($data['zip_code']);
            $listing->setCity($city);
            $listing->setDescription($data['description']);

            $listing->setPublicationDate(new \DateTime());
            $expirationDate = $listing->getPublicationDate()
                ->add(new \DateInterval($period->getIntervalSpec()));
            $listing->setExpirationDate($expirationDate);

            $listing->setUser($user);

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
    public function getCreateListingViolations(array $data)
    {
        $rules = $this->getValidationRules();

        return $this->getViolations($data, $rules);
    }

    /**
     * Update listing by given data
     *
     * @param Listing $listing Listing to update
     * @param $data array which contains information about listing
     *    $data = [
     *      'section_id' => (int) Section id. Optional.
     *      'title' => (string) Title. Optional.
     *      'zip_code' => (string) Zip-code. Optional.
     *      'city_id' => (int) City id. Optional.
     *      'description' => (string) Description. Optional.
     *      'period_id' => (int) Period id. Optional.
     *    ]
     * @return Listing|string Listing or error message
     */
    public function updateListing(Listing $listing, array $data)
    {
        $violations = $this->getUpdateListingViolations($data);
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
     * Validate listing data and get violations (if any)
     *
     * @param $data array which contains information about listing
     *    $data = [
     *      'section_id' => (int) Section id. Optional.
     *      'title' => (string) Title. Optional.
     *      'zip_code' => (string) Zip-code. Optional.
     *      'city_id' => (int) City id. Optional.
     *      'description' => (string) Description. Optional.
     *      'period_id' => (int) Period id. Optional.
     *      'user_id' => (int) User id. Optional.
     *    ]
     * @return ConstraintViolationList
     */
    public function getUpdateListingViolations(array $data)
    {
        $rules = $this->getValidationRules();

        // what to update (which optional fields are actually set in $data)?
        $updateRules = [];
        $updateKeys = array_keys($data);
        foreach ($updateKeys as $key) {
            if (isset($rules[$key])) {
                $updateRules[$key] = $rules[$key];
            }
        }

        return $this->getViolations($data, $updateRules);
    }

    /**
     * Validation rules to validate data during listing creation and update.
     */
    private function getValidationRules()
    {

        $rules = array(
            "section_id" => new Assert\Type(array("type" => "integer", "message" => "Unexpected section_id")),
            "zip_code" => new ListingAssert\ContainsGermanZipCode(),
            "city_id" => new Assert\Type(array("type" => "integer", "message" => "Unexpected city_id")),
            "title" => new Assert\Length(array(
                "min" => 5,
                "max" => 50,
                "minMessage" => "Title must be at least {{ limit }} characters long",
                "maxMessage" => "Title cannot be longer than {{ limit }} characters",
            )),
            "description" => new Assert\Length(array(
                "min" => 50,
                "max" => 500,
                "minMessage" => "Description must be at least {{ limit }} characters long",
                "maxMessage" => "Description cannot be longer than {{ limit }} characters",
            )),
            "period_id" => new Assert\Type(array("type" => "integer", "message" => "Unexpected period_id")),
            "user_id" => new Assert\Email(array("message" => "Unexpected user_id")),
        );

        return $rules;
    }

    /**
     * @param Listing $listing
     * @return bool|string True if listing was successfully deleted, error message otherwise
     */
    public function deleteListing(Listing $listing)
    {
        try {
            $this->em->remove($listing);
            $this->em->flush();
        } catch (\Exception $ex) {
            return "Unable to remove listing";
        }
        return true;
    }

    /**
     * @param array $filter
     * @return array
     */
    public function getListings(array $filter): array
    {
        $listings = $this->em
            ->getRepository(Listing::class)
            ->findAllFiltered($filter);

        return $listings;
    }
}