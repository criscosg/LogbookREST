<?php
namespace EasyScrumREST\TaskBundle\Entity;

use EasyScrumREST\UserBundle\Entity\ApiUser;
use Doctrine\ORM\Mapping as ORM;
use EasyScrumREST\TaskBundle\Entity\Task;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\MaxDepth;

/**
 * @ORM\Entity(repositoryClass="HoursSpentRepository")
 * @ExclusionPolicy("all")
 */

class HoursSpent
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @Expose
     */
    protected $id;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $hoursSpent;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $hoursEnd;
    
    /**
     * @ORM\ManyToOne(targetEntity="EasyScrumREST\TaskBundle\Entity\Task", inversedBy="listHours")
     * @Expose
     */
    private $task;
    
    /**
     * @ORM\ManyToOne(targetEntity="EasyScrumREST\TaskBundle\Entity\Urgency", inversedBy="listHours")
     * @Expose
     */
    private $urgency;
    
    /**
     * @ORM\ManyToOne(targetEntity="EasyScrumREST\UserBundle\Entity\User")
     * @Expose
     */
    private $user;
    
    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime", nullable=true)
     * @Assert\Date()
     * @Expose
     */
    protected $created;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getHoursSpent()
    {
        return $this->hoursSpent;
    }
    
    public function setHoursSpent($hoursSpent)
    {
        $this->hoursSpent = $hoursSpent;
    }
    
    public function getHoursEnd()
    {
        return $this->hoursEnd;
    }
    
    public function setHoursEnd($hoursEnd)
    {
        $this->hoursEnd = $hoursEnd;
    }
    
    public function getTask()
    {
        return $this->task;
    }
    
    public function setTask(Task $task)
    {
        $this->task = $task;
    }
    
    public function getUrgency()
    {
        return $this->urgency;
    }
    
    public function setUrgency(Urgency $urgency)
    {
        $this->urgency = $urgency;
    }
    
    public function getUser()
    {
        return $this->user;
    }
    
    public function setUser(ApiUser $user)
    {
        $this->user = $user;
    }
    
    public function getCreated()
    {
        return $this->created;
    }
    
    public function setCreated($created)
    {
        $this->created = $created;
    }
}