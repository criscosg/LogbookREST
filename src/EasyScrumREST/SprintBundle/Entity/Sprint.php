<?php
namespace EasyScrumREST\SprintBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use EasyScrumREST\TaskBundle\Entity\Task;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use EasyScrumREST\UserBundle\Entity\Company;
use JMS\Serializer\Annotation\MaxDepth;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="SprintRepository")
 * @ExclusionPolicy("all")
 */

class Sprint
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @Expose
     * */
    protected $id;

    /**
     * @ORM\Column(name="salt", type="string", length=255, nullable=true)
     * @Expose
     */
    protected $salt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="modified", type="datetime", nullable=true)
     */
    protected $modified;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     * @Expose
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Expose
     */
    protected $description;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="EasyScrumREST\TaskBundle\Entity\Task", mappedBy="log", cascade={"persist", "merge", "remove"})
     * @Expose
     * @MaxDepth(0)
     */
    private $tasks;

    /**
     * @ORM\ManyToOne(targetEntity="EasyScrumREST\UserBundle\Entity\Company", inversedBy="sprints")
     * @Expose
     */
    private $company;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime", nullable=true)
     * @Assert\Date()
     * @Expose
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\Date()
     * @Expose
     */
    protected $deleted;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $focus;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $hoursAvailable;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $hoursPlanified;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $hoursSpent;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $finalFocus;

    public function __construct()
    {
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getModified()
    {
        return $this->modified;
    }

    public function setModified($modified)
    {
        $this->modified = $modified;
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
        if (isset($this->name)) {
            return strval($this->name);
        } else {
            return strval($this->id);
        }

    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getTasks()
    {
        return $this->tasks;
    }

    public function setTasks(ArrayCollection $tasks)
    {
        $this->tasks = $tasks;
    }

    public function addTask(Task $task)
    {
        $this->tasks->add($task);
    }

    public function getCompany()
    {
        return $this->company;
    }

    public function setCompany(Company $company)
    {
        $this->company = $company;
    }

    public function getDeleted()
    {
        return $this->deleted;
    }

    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
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

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getFocus()
    {
        return $this->focus;
    }

    public function setFocus($focus)
    {
        $this->focus = $focus;
    }

    public function getHoursAvailable()
    {
        return $this->hoursAvailable;
    }

    public function setHoursAvailable($hoursAvailable)
    {
        $this->hoursAvailable = $hoursAvailable;
    }

    public function getHoursPlanified()
    {
        return $this->hoursPlanified;
    }

    public function setHoursPlanified($hoursPlanified)
    {
        $this->hoursPlanified = $hoursPlanified;
    }

    public function getHoursSpent()
    {
        return $this->hoursSpent;
    }

    public function setHoursSpent($hoursSpent)
    {
        $this->hoursSpent = $hoursSpent;
    }

    public function getFinalFocus()
    {
        return $this->finalFocus;
    }

    public function setFinalFocus($finalFocus)
    {
        $this->finalFocus = $finalFocus;
    }

}
