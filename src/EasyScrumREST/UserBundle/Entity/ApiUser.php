<?php
namespace EasyScrumREST\UserBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;

use EasyScrumREST\SprintBundle\Entity\Log;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use EasyScrumREST\UserBundle\Entity\User;
use EasyScrumREST\MessageBundle\Entity\Message;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\ExclusionPolicy;

/**
 * @ORM\Entity()
 * @ExclusionPolicy("all")
 */

class ApiUser extends User
{
    /**
     * @ORM\ManyToOne(targetEntity="Company", inversedBy="users", cascade={"persist"})
     * @Expose
     */
    private $company;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="EasyScrumREST\MessageBundle\Entity\Message", mappedBy="user", cascade={"persist", "merge", "remove"})
     */
    private $messages;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     * @Expose
     */
    private $roles;

    public function __construct()
    {
        $this->messages = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function setCompany(Company $company)
    {
        $this->company = $company;
    }

    public function getCompany()
    {
        return $this->company;
    }

    public function getMessages()
    {
        return $this->messages;
    }

    public function setMessages(ArrayCollection $messages)
    {
        $this->messages = $messages;
    }

    public function addMessage(Message $message)
    {
        $this->messages->add($message);
    }

    public function getRoles()
    {
        if ($this->roles){
            return array('ROLE_API_USER', $this->roles);
        }

        return array('ROLE_API_USER');
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

}
