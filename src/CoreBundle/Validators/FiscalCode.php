<?php
namespace CoreBundle\Validators;

use CoreBundle\Validators\Constraints\FiscalCodeValidator;
use Symfony\Component\Validator\Constraint;


/**
 * @Annotation
 */
class FiscalCode extends Constraint
{
    public $message = 'Il codice fiscale {{ string }} non è valido';

    public function validatedBy()
    {
        return FiscalCodeValidator::class;
    }

}