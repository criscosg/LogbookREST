<?php

namespace EasyScrumREST\ProjectBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use EasyScrumREST\ProjectBundle\Entity\Project;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\MaxDepth;
use JMS\Serializer\Annotation\Type;

/**
 * This entity refers to a user story in a project
 *
 * @ORM\Entity(repositoryClass="BacklogRepository")
 * @ExclusionPolicy("all")
 * @ORM\HasLifecycleCallbacks()
 */

class Backlog
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @Expose
     */
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
     * @ORM\ManyToOne(targetEntity="EasyScrumREST\ProjectBundle\Entity\Project", inversedBy="backlogs")
     * @Expose
     */
    private $project;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime", nullable=true)
     * @Assert\Date()
     * @Expose
     * @Type("DateTime<'d/m/Y H:i'>")
     */
    protected $created;

    /**
     * @ORM\Column(type="integer", length=50, nullable=true)
     * @Expose
     */
    protected $priority;

    /**
     * @ORM\Column(type="integer", nullable=true)
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
     * @ORM\OneToMany(targetEntity="EasyScrumREST\ProjectBundle\Entity\Issue", mappedBy="backlog", cascade={"persist", "merge", "remove"})
     * @Expose
     * @MaxDepth(0)
     */
    private $issues;
    
    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="EasyScrumREST\ActionBundle\Entity\ActionBacklog", mappedBy="backlog", cascade={"persist", "remove"})
     */
    private $actions;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Expose
     */
    protected $points;
    
    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="EasyScrumREST\TaskBundle\Entity\Task", mappedBy="story", cascade={"persist"})
     */
    private $tasks;

    public function __construct()
    {
        $this->issues = new \Doctrine\Common\Collections\ArrayCollection();
        $this->actions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
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

    public function getProject()
    {
        return $this->project;
    }

    public function setProject(Project $project)
    {
        $this->project = $project;
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
        $this->description=$description;
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

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
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
    
    public function getIssues()
    {
        return $this->issues;
    }
    
    public function setIssues(ArrayCollection $issues)
    {
        $this->issues = $issues;
    }
    
    public function addIssue(Issue $issue)
    {
        $this->issues->add($issue);
    }
    
    public function getActions()
    {
        return $this->actions;
    }
    
    public function setActions(ArrayCollection $actions)
    {
        $this->actions = $actions;
    }

    /**
     * @param mixed $points
     */
    public function setPoints($points)
    {
        $this->points = $points;
    }

    /**
     * @return mixed
     */
    public function getPoints()
    {
        return $this->points;
    }

    public function getTasks()
    {
        return $this->tasks;
    }
    
    public function setTasks(ArrayCollection $tasks)
    {
        $this->tasks = $tasks;
    }
    
    public function storyPointsLeft()
    {
        $points= $this->points;
        foreach ($this->tasks as $task) {
            $points -= $task->getCompletedPoints();
        }

        return $points;
    }
    
    public function storyPointsCompleted()
    {
        $points= 0;
        foreach ($this->tasks as $task) {
            $points += $task->getCompletedPoints();
        }
    
        return $points;
    }
}
