<?php

namespace EasyScrumREST\MessageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\MaxDepth;
use EasyScrumREST\UserBundle\Entity\ApiUser;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="MessageRepository")
 * @ExclusionPolicy("all")
 */

class Message
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @Expose
     * */
    private $id;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime", nullable=true)
     * @Assert\Date()
     * @Expose
     */
    private $created;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Expose
     */
    private $text;

    /**
     * @ORM\ManyToOne(targetEntity="EasyScrumREST\UserBundle\Entity\ApiUser", inversedBy="messages")
     * @Expose
     * @MaxDepth(1)
     */
    private $user;

    public function getId()
    {
        return $this->id;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function setCreated($created)
    {
        $this->created = $created;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(ApiUser $user)
    {
        $this->user=$user;
    }

}