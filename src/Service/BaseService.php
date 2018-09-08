<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

abstract class BaseService
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Validate data and get violations (if any)
     *
     * @param array $data which contains data to validate
     * @param array $rules Specifies which keys in data and how to validate. All keys will be validated by default.
     * @return ConstraintViolationList
     */
    protected function getViolations(array $data, array $rules = null): ConstraintViolationList
    {
        $validator = Validation::createValidator();

        $constraint = new Assert\Collection([
            'fields' => $rules,
            // even though it's anomaly, currently we don't care if there are unrelated fields in $data
            'allowExtraFields' => true,
        ]);
        $violations = $validator->validate($data, $constraint);

        return $violations;
    }

    /**
     * Convert array of violations (if any) to string with specified delimiter
     *
     * @param $violations
     * @return string
     */
    protected function getErrorsStr($violations)
    {
        $errorDelimiter = "###";

        $errors = [];
        foreach ($violations as $violation) {
            $errorMessage = $violation->getMessage();
            $errors[] = $errorMessage;
        }

        $errors = implode($errorDelimiter, $errors);

        return $errors;
    }
}