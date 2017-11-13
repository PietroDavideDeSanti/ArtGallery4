<?php

namespace CoreBundle\Services\AclHandler;

use CoreBundle\Interfaces\UserDataHandlerInterface;
use CoreBundle\Protocol\GlobalVars;
use CoreBundle\Protocol\UserVars;
use CoreBundle\Services\MyException;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AclHandler
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * SessionUserDataHandler constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function validateAction($element_code, UserVars $userVars) {
        $element = $this->em->getRepository("portalBundle:Element")->getElementByCode($element_code);
        //verifico se elemento è pubblico
        if($element->getCode()){
            return true;
        }
        //se non è un elemento pubblico verifico il profilo
        $element = $this->em->getRepository("portalBundle:Element")->getElementByCodeAndProfile($element->getCode(), $userVars->profile);
        if(!$element) {
            throw new HttpException(MyException::FORBIDDEN_STATUS, "||Accesso non autorizzato");
        }
        return true;
    }
}