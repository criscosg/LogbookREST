<?php
namespace EasyScrumREST\TaskBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use EasyScrumREST\SprintBundle\Entity\Sprint;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @ORM\Column(name="modified", type="datetime", nullable=true)
     */
    protected $modified;

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
     * @ORM\ManyToMany(targetEntity="EasyScrumREST\TaskBundle\Entity\Tag", mappedBy="tasks")
     * @Expose
     */
    private $tags;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime", nullable=true)
     * @Assert\Date()
     * @Expose
     */
    protected $created;

    /**
     * @ORM\Column(type="string", length=50)
     * @Expose
     */
    protected $priority;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $hours;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * */
    protected $hoursSpent;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $hoursEnd;

    public function __construct()
    {
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
        return $this->name;
    }

    public function getSprint()
    {
        return $this->sprint;
    }

    public function setSprint(Sprint $sprint)
    {
        $this->sprint = $sprint;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function setTags(ArrayCollection $tags)
    {
        $this->tags = $tags;
    }

    public function addTag(Task $tag)
    {
        $this->tags->add($tag);
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

}
