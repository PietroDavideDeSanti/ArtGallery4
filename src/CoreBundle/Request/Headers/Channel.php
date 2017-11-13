<?php

namespace CoreBundle\Request\Headers;

use Symfony\Component\Validator\Constraints as Assert;


class Channel {

    /**
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    protected $channel;


    public function getChannel()
    {
        return $this->channel;
    }

    public function setChannel($channel)
    {
        $this->channel = $channel;
    }


}
