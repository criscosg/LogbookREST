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
 */

class Task
{
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
     * @Expose
     */
    private $sprint;

    /**
     * @ORM\ManyToOne(targetEntity="EasyScrumREST\TaskBundle\Entity\Category", inversedBy="tasks")
     * @Expose
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

    public function __construct()
    {
        $this->listHours = new \Doctrine\Common\Collections\ArrayCollection();
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
        for ($i=0; $i < $this->getListHours()->count(); $i++) {
            $elem=$this->getListHours()->get($i);
            $totalHours+=$elem->getHoursSpent();
        }

        return $totalHours;
    }

    public function getHoursEnd()
    {
        if($this->getListHours()->count()>0){
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

}
