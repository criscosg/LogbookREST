<?php
namespace EasyScrumREST\ProjectBundle\Entity;

use EasyScrumREST\UserBundle\Entity\ApiUser;

use EasyScrumREST\SprintBundle\Entity\Sprint;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use EasyScrumREST\UserBundle\Entity\Company;
use JMS\Serializer\Annotation\MaxDepth;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity()
 * @ExclusionPolicy("all")
 */

class Project
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
     * @ORM\OneToMany(targetEntity="EasyScrumREST\SprintBundle\Entity\Sprint", mappedBy="project", cascade={"persist", "merge", "remove"})
     * @Expose
     * @MaxDepth(0)
     */
    private $sprints;
    
    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="EasyScrumREST\ProjectBundle\Entity\Backlog", mappedBy="project", cascade={"persist", "merge", "remove"})
     * @Expose
     * @MaxDepth(0)
     */
    private $backlogs;

    /**
     * @ORM\ManyToOne(targetEntity="EasyScrumREST\UserBundle\Entity\Company", inversedBy="projects")
     * @Expose
     */
    private $company;
    
    /**
     * @ORM\ManyToOne(targetEntity="EasyScrumREST\UserBundle\Entity\ApiUser", inversedBy="projects")
     * @Expose
     */
    private $owner;

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

    public function __construct()
    {
        $this->sprints = new \Doctrine\Common\Collections\ArrayCollection();
        $this->backlogs = new \Doctrine\Common\Collections\ArrayCollection();
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

    public function getSprints()
    {
        return $this->sprints;
    }
    
    public function setSprints(ArrayCollection $sprints)
    {
        $this->sprints = $sprints;
    }
    
    public function addSprint(Sprint $sprint)
    {
        $this->sprints->add($sprint);
    }
    
    public function getBacklogs()
    {
        return $this->backlogs;
    }
    
    public function setBacklogs(ArrayCollection $backlogs)
    {
        $this->backlogs = $backlogs;
    }
    
    public function addBacklog(Backlog $backlog)
    {
        $this->backlogs->add($backlog);
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
    
    public function getOwner()
    {
        return $this->owner;
    }
    
    public function setOwner(ApiUser $owner)
    {
        $this->owner = $owner;
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
}