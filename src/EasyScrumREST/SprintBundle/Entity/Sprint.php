<?php
namespace EasyScrumREST\SprintBundle\Entity;
use EasyScrumREST\ProjectBundle\Entity\Project;

use EasyScrumREST\TaskBundle\Entity\Urgency;

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
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    protected $updated;

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
     * @ORM\OneToMany(targetEntity="EasyScrumREST\TaskBundle\Entity\Task", mappedBy="sprint", cascade={"persist", "merge", "remove"})
     * @Expose
     * @MaxDepth(0)
     */
    private $tasks;
    
    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="EasyScrumREST\TaskBundle\Entity\Urgency", mappedBy="sprint", cascade={"persist", "merge", "remove"})
     * @Expose
     * @MaxDepth(0)
     */
    private $urgencies;

    /**
     * @ORM\ManyToOne(targetEntity="EasyScrumREST\UserBundle\Entity\Company", inversedBy="sprints")
     * @Expose
     */
    private $company;
    
    /**
     * @ORM\ManyToOne(targetEntity="EasyScrumREST\ProjectBundle\Entity\Project", inversedBy="sprints")
     * @Expose
     */
    private $project;

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

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\Date()
     * @Expose
     */
    protected $fromDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\Date()
     * @Expose
     */
    protected $toDate;
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $planified;
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $finalized;

    public function __construct()
    {
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
        $this->urgencies = new \Doctrine\Common\Collections\ArrayCollection();
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
        if (isset($this->title)) {
            return strval($this->title);
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
    
    public function getUrgencies()
    {
        return $this->urgencies;
    }
    
    public function setUrgencies(ArrayCollection $urgencies)
    {
        $this->urgencies = $urgencies;
    }
    
    public function addUrgency(Urgency $urgency)
    {
        $this->urgencies->add($urgency);
    }

    public function getCompany()
    {
        return $this->company;
    }

    public function setCompany(Company $company)
    {
        $this->company = $company;
    }

    public function getProject()
    {
        return $this->project;
    }

    public function setProject(Project $project)
    {
        $this->project = $project;
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

    public function getFromDate()
    {
        return $this->fromDate;
    }

    public function setFromDate($fromDate)
    {
        $this->fromDate = $fromDate;
    }

    public function getToDate()
    {
        return $this->toDate;
    }

    public function setToDate($toDate)
    {
        $this->toDate = $toDate;
    }
    
    public function getFinalized()
    {
        return $this->finalized;
    }
    
    public function setFinalized($finalized)
    {
        $this->finalized = $finalized;
    }

    public function getPlanified()
    {
        return $this->planified;
    }
    
    public function setPlanified($planified)
    {
        $this->planified = $planified;
    }
    
    public function getPlanificationHours()
    {
        $total=0;
        foreach ($this->getTasks() as $Urgency) {
            $total += $task->getHours();
        }
    
        return $total;
    }
    
    public function getSpentHours()
    {
        $total=0;
        foreach ($this->getTasks() as $task) {
            if ($task->getHoursSpent()){
                $total += $task->getHoursSpent();
            }
        }
        
        return $total;
    }
    
    public function getUrgenciesSpentHours()
    {
        $total=0;
        foreach ($this->getUrgencies() as $urgency) {
            if ($urgency->getHoursSpent()){
                $total += $urgency->getHoursSpent();
            }
        }
    
        return $total;
    }
    
    public function getTaskUndone()
    {
        $undone=array();
        foreach ($this->getTasks() as $task) {
            if ($task->getState() != "DONE" && (($task->getHoursEnd() && $task->getHoursEnd() != 0) || !$task->getHoursEnd()) ){
                $undone[] = $task;
            } elseif($task->getState() == "UNDONE") {
                $undone[] = $task;
            }
        }
    
        return $undone;
    }
    
    public function getHoursUndone()
    {
        $total=0;
        foreach ($this->getTasks() as $task) {
            if($task->getState() == "UNDONE") {
                $total = ($task->getHoursEnd())? $total + $task->getHoursEnd():$total + $task->getHours();
            } else if ($task->getState() != "DONE" && (($task->getHoursEnd() && $task->getHoursEnd() != 0) || !$task->getHoursEnd()) ){
                $total = ($task->getHoursEnd())? $total + $task->getHoursEnd():$total + $task->getHours();
            } 
        }
    
        return $total;
    }
    
    public function getHoursDone()
    {
        $total=0;
        foreach ($this->getTasks() as $task) {
            if($task->getState() == "DONE") {
                $total = ($task->getHoursEnd())? $total + $task->getHoursEnd():$total + $task->getHours();
            } elseif ($task->getState() != "UNDONE" && ($task->getHoursEnd() && $task->getHoursEnd() == 0)){
                $total = ($task->getHoursEnd())? $total + $task->getHoursEnd():$total + $task->getHours();
            } 
        }
    
        return $total;
    }
    
    public function getTaskDone()
    {
        $done=array();
        foreach ($this->getTasks() as $task) {
            if($task->getState() == "DONE") {
                $done[] = $task;
            } elseif ($task->getState() != "UNDONE" && ($task->getHoursEnd() && $task->getHoursEnd() == 0)){
                $done[] = $task;
            }
        }

        return $done;
    }
}
