<?php

namespace CoreBundle\Request\Headers;

use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @author K2
 * Per la documentazione sugli Assert, vedere qui:
 * http://symfony.com/doc/current/validation.html
 *
 */
class Authorization {

    /**
     * @Assert\NotNull(message="§Il valore dell'header Authorization  non puo' essere null§")
     * @Assert\NotBlank(message="§Il valore dell'header Authorization  non puo' essere blank§")
     * @Assert\Type(type="string", message="§Il valore dell'header Authorization deve essere di tipo {{ type }}§")
     * @Assert\Regex(
     *     pattern="/Bearer [\w=+-\\\/]+/",
     *     match=true,
     *     message="§Sintassi del token non corretta (cfr. Bearer Usage)§"
     * )
     */
    protected $authorization;


    public function getAuthorization()
    {
        return $this->authorization;
    }

    public function setAuthorization($authorization)
    {
        $this->authorization = $authorization;
    }


}
