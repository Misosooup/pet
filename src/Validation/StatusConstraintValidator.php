<?php

namespace App\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class StatusConstraintValidator extends ConstraintValidator
{
    private $whileListStatus = ['pending', 'sold', 'available'];
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $value      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!in_array($value, $this->whileListStatus)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}