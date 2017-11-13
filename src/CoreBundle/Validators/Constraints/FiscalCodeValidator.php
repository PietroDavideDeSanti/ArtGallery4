<?php

namespace CoreBundle\Validators\Constraints;

use CoreBundle\Services\MyException;
use CoreBundle\Validators\Libraries\FiscalCode;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class FiscalCodeValidator extends ConstraintValidator
{

    public function validate($value, Constraint $constraint)
    {
        $fiscalCode = $value;

        $validator = new FiscalCode();
        $validator->validate($fiscalCode);

    }
}