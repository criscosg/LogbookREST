<?php
namespace EasyScrumREST\TaskBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use EasyScrumREST\SprintBundle\Entity\Sprint;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\MaxDepth;

/**
 * @ORM\Entity(repositoryClass="TaskRepository")
 * @ExclusionPolicy("all")
 * @ORM\HasLifecycleCallbacks()
 */
class Task
{
    const TODO="TODO";
    const ONPROCESS = "ONPROCESS";
    const DONE = "DONE";
    const UNDONE ="UNDONE";
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @Expose
     * */
    protected $id;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    protected $updated;

    /**
     * @ORM\Column(type="string", length=250)
     * @Expose
     */
    protected $title;

    /**
     * @ORM\Column(type="text", length=250, nullable=true)
     * @Expose
     */
    protected $description;

    /**
     * @ORM\ManyToOne(targetEntity="EasyScrumREST\SprintBundle\Entity\Sprint", inversedBy="tasks")
     */
    private $sprint;

    /**
     * @ORM\ManyToOne(targetEntity="EasyScrumREST\TaskBundle\Entity\Category", inversedBy="tasks")
     */
    private $category;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime", nullable=true)
     * @Assert\Date()
     * @Expose
     */
    protected $created;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Expose
     */
    protected $priority;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Expose
     */
    protected $hours;

    /**
     * @ORM\Column(name="salt", type="string", length=255, nullable=true)
     * @Expose
     */
    protected $salt;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Expose
     */
    protected $state = "TODO";

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="EasyScrumREST\TaskBundle\Entity\HoursSpent", mappedBy="task", cascade={"persist", "merge", "remove"})
     * @Expose
     * @MaxDepth(0)
     */
    private $listHours;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="EasyScrumREST\ActionBundle\Entity\ActionTask", mappedBy="task", cascade={"persist", "remove"})
     */
    private $actions;
    
    /**
     * @ORM\ManyToOne(targetEntity="EasyScrumREST\UserBundle\Entity\ApiUser", inversedBy="tasks")
     */
    private $user;

    public function __construct()
    {
        $this->listHours = new \Doctrine\Common\Collections\ArrayCollection();
        $this->actions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function setCreated($created)
    {
        $this->created = $created;
    }

    public function __toString()
    {
        return $this->title;
    }

    public function getSprint()
    {
        return $this->sprint;
    }

    public function setSprint(Sprint $sprint)
    {
        $this->sprint = $sprint;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory(Category $category)
    {
        $this->category = $category;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    public function getHours()
    {
        return $this->hours;
    }

    public function setHours($hours)
    {
        $this->hours = $hours;
    }

    public function getHoursSpent()
    {
        $totalHours = 0;
        for ($i = 0; $i < $this->getListHours()->count(); $i++) {
            $elem = $this->getListHours()->get($i);
            $totalHours += $elem->getHoursSpent();
        }

        return $totalHours;
    }

    public function getHoursEnd()
    {
        if ($this->getListHours()->count() > 0) {
            return $this->getListHours()->last()->getHoursEnd();
        }

        return null;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
    }
    /**
     * 
     * @return \Doctrine\Common\Collections\ArrayCollection
     */

    public function getListHours()
    {
        return $this->listHours;
    }

    public function setListHours(ArrayCollection $hours)
    {
        $this->listHours = $hours;
    }

    public function addListHour(HoursSpent $spent)
    {
        $this->listHours->add($spent);
    }

    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @ORM\PrePersist
     */
    public function setSalt($salt = null)
    {
        if (!$this->salt) {
            if (!$salt) {
                $salt = md5(time());
            }
            $this->salt = $salt;
        }
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user=null)
    {
        $this->user = $user;
    }
    
    public function getTaskNotifications()
    {
        $notifications=array();
        if($this->hoursOverPlanified()) {
            $notifications['Task is taking to much time']= 'The task "' . $this->title . '" has exceeded the planified time given.';
        }
        if($this->notUpdated()){
            $notifications['Task not updated']= 'The task "' . $this->title . '" is on process and have not been updated for more than 2 days.';
        }
    
        return $notifications;
    }
    
    private function hoursOverPlanified()
    {
        if ($this->state == self::ONPROCESS || $this->state == self::TODO) {
            if($this->getHoursSpent() > ($this->getHours() * 1.2)) {
                return true;
            }
        }
    
        return null;
    }
    
    private function notUpdated()
    {
        if ($this->state == self::ONPROCESS) {
            $now=new \DateTime('today');
            if($this->getListHours()->count() == 0){
                $this->updated->add(new \DateInterval('P2D'));
                if($now > $this->updated) {
                    return true;
                }
            } else {
                $this->getListHours()->last()->getCreated()->add(new \DateInterval('P2D'));
                if ($now > $this->getListHours()->last()->getCreated()) {
                    return true;
                }
            }
        }

        return null;
    }

    public function getActions()
    {
        return $this->actions;
    }

    public function setActions(ArrayCollection $actions)
    {
        $this->actions = $actions;
    }

}
