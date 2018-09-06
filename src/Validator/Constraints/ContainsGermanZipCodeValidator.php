<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class ContainsGermanZipCodeValidator
 * @package App\Validator\Constraints
 *
 * How to build custom constraint docs: https://symfony.com/doc/current/validation/custom_constraint.html
 *
 */
class ContainsGermanZipCodeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        // Zip-code check is taken from here:
        //  https://stackoverflow.com/questions/7926687/regular-expression-german-zip-codes
        if (!preg_match('/^0[1-9]\d\d(?<!0100)0|0[1-9]\d\d[1-9]|[1-9]\d{3}[0-8]|[1-9]\d{3}(?<!9999)9$/', $value, $matches)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}