<?php
namespace EasyScrumREST\UserBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use EasyScrumREST\SprintBundle\Entity\Sprint;

/**
 * @ORM\Entity()
 * @ExclusionPolicy("all")
 */

class Company
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
    protected $name;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime", nullable=true)
     * @Assert\Date()
     * @Expose
     */
    protected $created;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="ApiUser", mappedBy="company", cascade={"persist", "merge", "remove"})
     */
    private $users;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="EasyScrumREST\SprintBundle\Entity\Sprint", mappedBy="company", cascade={"persist", "merge", "remove"})
     * @Expose
     */
    private $sprints;
    
    /**
     * @ORM\Column(type="float", precision=2, nullable=true)
     */
    private $pricePerHour;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $hoursPerDay=8;

    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->sprints = new \Doctrine\Common\Collections\ArrayCollection();
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

    public function getUsers()
    {
        return $this->users;
    }

    public function setUsers(ArrayCollection $users)
    {
        $this->users = $users;
    }

    public function addUser(User $user)
    {
        $this->users->add($user);
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getSprints()
    {
        return $this->sprints;
    }

    public function setSprints($sprints)
    {
        $this->sprints = $sprints;
    }

    public function addSprint(Sprint $sprint)
    {
        $this->sprints->add($sprint);
    }
    
    public function getPricePerHour()
    {
        return $this->pricePerHour;
    }
    
    public function setPricePerHour($pricePerHour)
    {
        $this->pricePerHour = $pricePerHour;
    }
    
    public function getHoursPerDay()
    {
        return $this->hoursPerDay;
    }
    
    public function setHoursPerDay($hoursPerDay)
    {
        $this->hoursPerDay = $hoursPerDay;
    }
}
