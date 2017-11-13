<?php

namespace CoreBundle\Interfaces;

use CoreBundle\Protocol\GlobalVars;
use CoreBundle\Protocol\UserVars;

interface UserDataHandlerInterface
{

    /**
     * @param GlobalVars $globalVars
     * @return GlobalVars
     */
    public function setUserDataProvider(GlobalVars $globalVars);

    /**
     * @param GlobalVars $globalVars
     * @return UserVars
     */
    public function setUserDataConsumer(GlobalVars $globalVars);

}