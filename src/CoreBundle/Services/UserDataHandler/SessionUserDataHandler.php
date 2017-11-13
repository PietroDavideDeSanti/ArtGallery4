<?php

namespace CoreBundle\Services\UserDataHandler;

use CoreBundle\Interfaces\UserDataHandlerInterface;
use CoreBundle\Protocol\GlobalVars;
use CoreBundle\Protocol\UserVars;
use CoreBundle\Services\MyException;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SessionUserDataHandler implements UserDataHandlerInterface
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

    /**
     * @param GlobalVars $globalVars
     * @return GlobalVars
     */
    public function setUserDataProvider(GlobalVars $globalVars)
    {
        $userVars = new UserVars();

        if (isset($_SESSION['SUserData']['Sute_biideute'])) {
            $userVars->id = $_SESSION['SUserData']['Sute_biideute'];
        } else {
            throw new HttpException(MyException::UNAUTHORIZED_STATUS, "||Acceso non autorizzato");
        }

        $globalVars->user = $userVars;

        return $globalVars;
    }

    /**
     * @param GlobalVars $globalVars
     * @return UserVars
     */
    public function setUserDataConsumer(GlobalVars $globalVars){

        $userVars = $globalVars->user;

        // Dealer:
        if (isset($_SESSION['SUserData']['Sute_biidecon'])) {
            $userVars->dealer = $_SESSION['SUserData']['Sute_biidecon'];
        } else {
            $dealerDB = $this->em->getRepository("portalBundle:Dealer")->getDealerIdByUserId($userVars->id);
            $userVars->dealer = $dealerDB;
            $_SESSION['SUserData']['Sute_biidecon'] = $dealerDB;
        }

        // Convenzione attiva:
        if (isset($_SESSION["SUserData"]["SActiveCnv"])) {
            $userVars->agreement = $_SESSION["SUserData"]["SActiveCnv"];
        } else {
            $activeAgreementDB = $this->em->getRepository("portalBundle:Agreement")->getActiveAgreementIdByUserId($userVars->id);
            $userVars->agreement = $activeAgreementDB;
            $_SESSION["SUserData"]["SActiveCnv"] = $activeAgreementDB;
        }

        // Profili:
        if (isset($_SESSION['SUserData']['Sute_biidepro'])) {
            $userVars->profile[] = $_SESSION['SUserData']['Sute_biidepro'];
        }
//
        // Recupero (se necessario) gli altri profili dal database:
        $profiles = array();
        if (isset($_SESSION['SUserData']['Sute_biidepro_add'])) {
            $profiles = $_SESSION['SUserData']['Sute_biidepro_add'];
        } else {
            $profilesDB = array();
            if($userVars->dealer != null && $userVars->agreement != null ) {
                $profilesDB = $this->em->getRepository("portalBundle:Profile")->getAllByUser($userVars->id);
            }
            foreach ($profilesDB as $profile) {
                $_SESSION['SUserData']['Sute_biidepro_add'][] = $profile->getId();
                $profiles[] = $profile->getId();
            }
        }

        // Aggiungo i profili recuperati all'oggetto UserVars:
        foreach ($profiles as $id) {
            $userVars->profile[] = $id;
        }

        // Convenzioni:
        $agreements = array();
        if (isset($_SESSION['SUserData']['Sute_vccodcon_add'])) {
            $agreements = $_SESSION['SUserData']['Sute_vccodcon_add'];
        } else {
            $agreementsDB = $this->em->getRepository("portalBundle:Agreement")->getAllByUser($userVars->id);
            foreach ($agreementsDB as $agreement) {
                $_SESSION['SUserData']['Sute_vccodcon_add'][] = $agreement->getId();
                $agreements[] = $agreement->getId();
            }
        }

        foreach ($agreements as $id) {
            $userVars->agreements[] = $id;
        }

        // Ufficio default:
        if (isset($_SESSION['SUserData']['Sute_biidesed'])) {
            $userVars->office = $_SESSION['SUserData']['Sute_biidesed'];
        } else {
            $dealerDB = $this->em->getRepository("portalBundle:Office")->getDefaultOfficeIdByUserId($userVars->id);
            $userVars->office = $dealerDB;
            $_SESSION['SUserData']['Sute_biidesed'] = $dealerDB;
        }

        // Uffici:
        $offices = array();
        if (isset($_SESSION['SUserData']['Sute_biidesed_add'])) {
            $offices = $_SESSION['SUserData']['Sute_biidesed_add'];
        } else {
            $officesDB = $this->em->getRepository("portalBundle:Office")->getAllByUser($userVars->id);
            foreach ($officesDB as $office) {
                $_SESSION['SUserData']['Sute_biidesed_add'][] = $office->getId();
                $offices[] = $office->getId();
            }
        }

        foreach ($offices as $id) {
            $userVars->offices[] = $id;
        }

        return $userVars;

    }

}