<?php

namespace CoreBundle\Services\ButtonHandler;

use CoreBundle\Interfaces\UserDataHandlerInterface;
use CoreBundle\Protocol\GlobalVars;
use CoreBundle\Protocol\UserVars;
use CoreBundle\Services\MyException;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ButtonHandler
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

    public function getAllButtons(array $arrContext, array $arrProfile) {

        $arrElementContextProfile = $this->em->getRepository("portalBundle:ElementContext")->getAllContextWithDetailByProfile($arrContext, $arrProfile);
        $this->em->clear(null);
        $arrElementContext = array();
        foreach($arrElementContextProfile as $contextProfile){
            $arrElementContext[$contextProfile->getName()] = array();
            foreach($contextProfile->getElementDetail() as $elementDetail) {

                $parent = !empty($elementDetail->getParentId()) ? $elementDetail->getParentId()->getName() : null;
                if(empty($parent)) {
                    $arrElementContext[$contextProfile->getName()][] = $elementDetail;
                } else {
                    $arrElementContext[$contextProfile->getName()][$parent][] = $elementDetail;
                }
            }
//            $arrElementContext[$contextProfile->getName()] = $contextProfile;
        }

        $arrElementContextPublic = $this->em->getRepository("portalBundle:ElementContext")->getAllContextWithDetailForPublicElement($arrContext, $arrProfile);
        $this->em->clear(null);
        foreach($arrElementContextPublic as $contextPublic){
            //verifica se il contesto esiste già
            if(!isset($arrElementContext[$contextPublic->getName()])) {
                $arrElementContext[$contextPublic->getName()] = array();
            }
            foreach($contextPublic->getElementDetail() as $elementDetail) {

                $parent = !empty($elementDetail->getParentId()) ? $elementDetail->getParentId()->getName() : null;
                if(empty($parent)) {
                    $arrElementContext[$contextPublic->getName()][] = $elementDetail;
                } else {
                    $arrElementContext[$contextPublic->getName()][$parent][] = $elementDetail;
                }
            }
//            $arrElementContext[$contextProfile->getName()] = $contextProfile;
        }

        return $arrElementContext;

    }

    /**
     * Funzione per rimuovere un button da un array
     */
    public function removeButtons($arrElementDetailToCheck, $arrElementDetailNameToRemove) {
        $return = array();
        foreach ($arrElementDetailToCheck as $ElementDetail) {
            if(!in_array($ElementDetail["name"], $arrElementDetailNameToRemove)) {
                $return[] = $ElementDetail;
            }
        }
        return $return;
    }

    public function disableButtons($arrElementDetailToCheck, $arrElementDetailNameToDisable, $disabledMessage = "") {
        $return = array();
        foreach ($arrElementDetailToCheck as $ElementDetail) {
            if(in_array($ElementDetail["name"], $arrElementDetailNameToDisable)) {
                $ElementDetail["is_disabled"] = 1;
                $ElementDetail["description"] = $disabledMessage;
            }
            $return[] = $ElementDetail;
        }
        return $return;
    }

    /* viene chiamata dal K2 controller soltanto -- NON USARE NEI MODEL!!! */
    public function addPropertyDisableButtons($arrContext){
        foreach($arrContext as $keyContext => $context){
            foreach($context as $keyElement => $elementDetail ){
                foreach($elementDetail as $row ) {
                    if (!is_array($row)) {
                        $arrContext[$keyContext][$keyElement]['is_disabled'] = 0;
                    }
                }
            }
        }
        return $arrContext;
    }
}