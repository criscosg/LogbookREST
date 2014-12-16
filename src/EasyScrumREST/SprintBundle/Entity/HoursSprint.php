<?php
namespace EasyScrumREST\SprintBundle\Entity;

use EasyScrumREST\SprintBundle\Util\DateHelper;
use EasyScrumREST\ProjectBundle\Entity\Project;
use EasyScrumREST\TaskBundle\Entity\Urgency;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="SprintRepository")
 */

class HoursSprint
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * */
    protected $id;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="date", nullable=true)
     * @Assert\Date()
     */
    protected $created;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $hours;
    
    /**
     * @ORM\Column(name="date_sprint", type="date", nullable=true)
     * @Assert\Date()
     */
    protected $date;

    /**
     * @ORM\ManyToOne(targetEntity="EasyScrumREST\SprintBundle\Entity\Sprint", inversedBy="listHours")
     */
    private $sprint;

    public function getId()
    {
        return $this->id;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function setCreated($created)
    {
        $this->created = $created;
    }
    
    public function getDate()
    {
        return $this->date;
    }
    
    public function setDate($date)
    {
        $this->date = $date;
    }

    public function getHours()
    {
        return $this->hours;
    }

    public function setHours($hours)
    {
        $this->hours = $hours;
    }

    public function getSprint()
    {
        return $this->sprint;
    }

    public function setSprint($sprint)
    {
        $this->sprint = $sprint;
    }

}