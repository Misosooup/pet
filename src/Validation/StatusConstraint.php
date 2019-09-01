<?php

namespace App\Validation;

use Symfony\Component\Validator\Constraint;

class StatusConstraint extends Constraint
{
    public $message = 'Invalid status provided';

    public function validateBy()
    {
        return StatusValidator::class;
    }
}