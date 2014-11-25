<?php
namespace EasyScrumREST\UserBundle\Entity;
use EasyScrumREST\ImageBundle\Entity\ImageProfile;

use Doctrine\Common\Collections\ArrayCollection;

use EasyScrumREST\SprintBundle\Entity\Log;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use EasyScrumREST\UserBundle\Entity\User;
use EasyScrumREST\MessageBundle\Entity\Message;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\ExclusionPolicy;

/**
 * @ORM\Entity(repositoryClass="UserRepository")
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
     * @var ImageProfile
     * @ORM\OneToOne(targetEntity="EasyScrumREST\ImageBundle\Entity\ImageProfile", mappedBy="user", cascade={"persist", "merge", "remove"})
     */
    private $profileImage;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     * @Expose
     */
    private $roles;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="EasyScrumREST\TaskBundle\Entity\Task", mappedBy="user")
     */
    private $tasks;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     * @Expose
     */
    private $color;
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $notification;

    public function __construct()
    {
        $this->messages = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
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

    public function getProfileImage()
    {
        return $this->profileImage;
    }

    public function setProfileImage(ImageProfile $profileImage)
    {
        $this->profileImage = $profileImage;
    }

    public function addMessage(Message $message)
    {
        $this->messages->add($message);
    }

    public function getRoles()
    {
        if ($this->roles) {
            return array('ROLE_API_USER', $this->roles);
        }

        return array('ROLE_API_USER');
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    public function getTasks()
    {
        return $this->tasks;
    }

    public function setTasks(ArrayCollection $tasks)
    {
        $this->tasks = $tasks;
    }

    public function getColor()
    {
        return $this->color;
    }

    public function setColor($color)
    {
        $this->color = $color;
    }
    
    public function getNotification()
    {
        return $this->notification;
    }
    
    public function setNotification($notification)
    {
        $this->notification = $notification;
    }

}
