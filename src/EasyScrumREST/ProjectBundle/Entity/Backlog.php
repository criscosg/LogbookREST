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

/**
 * @ORM\Entity()
 * @ExclusionPolicy("all")
 */

class Backlog
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
     * @ORM\ManyToOne(targetEntity="EasyScrumREST\ProjectBundle\Entity\Project", inversedBy="backlogs")
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

    public function __construct()
    {
        $this->issues = new \Doctrine\Common\Collections\ArrayCollection();
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

}
