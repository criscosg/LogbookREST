<?php
namespace EasyScrumREST\TaskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EasyScrumREST\TaskBundle\Entity\Horse;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="CategoryRepository")
 * @ExclusionPolicy("all")
 */

class Category
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
     * @ORM\Column(type="text")
     * @Expose
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="EasyScrumREST\TaskBundle\Entity\Task", mappedBy="category")
     */
    private $tasks;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime", nullable=true)
     * @Assert\Date()
     * @Expose
     */
    protected $created;

    public function __construct()
    {
        $this->tasks= new \Doctrine\Common\Collections\ArrayCollection();
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

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
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
        return $this->text;
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
}
