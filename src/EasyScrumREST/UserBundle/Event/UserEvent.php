<?php

namespace EasyScrumREST\UserBundle\Event;

use EasyScrumREST\UserBundle\Entity\RecoverPassword;
use Symfony\Component\EventDispatcher\Event;

class UserEvent extends Event
{
    private $recover;

    public function __construct(RecoverPassword $recover)
    {
        $this->recover = $recover;
    }

    /**
     * @return RecoverPassword $recover
     */
    public function getRecover()
    {
        return $this->recover;
    }
}