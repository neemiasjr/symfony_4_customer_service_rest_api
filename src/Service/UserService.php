<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;

class UserService extends BaseService
{
    /**
     * @param $email
     * @return User|string User entity or string in case of error
     */
    public function getUser($email)
    {
        $user = $this->em->getRepository(User::class)
            ->findOneBy(['email' => $email]);
        if ($user) {
            return $user;
        } else {
            return "No such user";
        }
    }

    /**
     * @param $data
     *    $data = [
     *      'name' => (string) User name. Required.
     *      'password' => (string) User (plain) password. Required.
     *    ]
     * @return User|string User entity or string in case of error
     */
    public function createUser($data)
    {
        $email = $data['email'];
        $plainPassword = $data['password'];
        $user = new User();
        $user->setEmail($email);
        $encoded = password_hash($plainPassword, PASSWORD_DEFAULT);
        $user->setPassword($encoded);
        try {
            $this->em->persist($user);
            $this->em->flush();

            return $user;
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex) {
            return "User with given email already exists";
        } catch (\Exception $ex) {
            return "Unable to create user";
        }
    }

    /**
     * Validate user data and get violations (if any)
     *
     * @param $data array which contains information about user
     *    $data = [
     *      'email' => (string) Title. Required.
     *      'password' => (string) User id. Required.
     *    ]
     * @return ConstraintViolationList
     */
    public function getCreateUserViolations($data)
    {
        $validator = Validation::createValidator();

        $constraint = new Assert\Collection(array(
            'email' => new Assert\Email(),
            'password' => new Assert\Length(array('min' => 5)),
        ));

        $violations = $validator->validate($data, $constraint);

        return $violations;
    }
}