<?php
namespace LogbookREST\OAuthBundle\Entity;
use FOS\OAuthServerBundle\Entity\AuthCode as BaseAuthCode;
use Doctrine\ORM\Mapping as ORM;
use LogbookREST\UserBundle\Entity\User;

/**
 * @ORM\Entity
 */
class AuthCode extends BaseAuthCode
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Client")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $client;

    /**
     * @var User $user
     * @ORM\ManyToOne(targetEntity="LogbookREST\UserBundle\Entity\User")
     */
    protected $user;
}
