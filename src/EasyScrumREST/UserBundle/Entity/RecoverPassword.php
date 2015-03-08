<?php

namespace EasyScrumREST\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class RecoverPassword
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=250)
     * @Assert\Email(checkMX=true)
     * */
    protected $email;

    /**
     * @var string salt
     *
     * @ORM\Column(name="salt", type="string", length=255)
     */
    protected $salt;

    /**
     * @var date $date
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="date", type="date", nullable=true)
     * @Assert\Date()
     */
    protected $dateRequest;

    public function getId()
    {
        return $this->id;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getDateRequest()
    {
        return $this->dateRequest;
    }

    public function setDateRequest($date)
    {
        $this->dateRequest = $date;
    }
}