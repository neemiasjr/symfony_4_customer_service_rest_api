<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ContainsGermanZipCode extends Constraint
{
    public $message = 'Zip-code "{{ string }}" is invalid: it can only be a German zip-code.';
}