<?php
namespace EasyScrumREST\ActionBundle\Entity;

use EasyScrumREST\SprintBundle\Entity\Sprint;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class ActionSprint extends Action
{
    const CREATE_SPRINT = "create_sprint";
    const EDIT_SPRINT = "edited_sprint";
    const FINALIZED_SPRINT = "finalized_sprint";
    const STATE_HOURS_SAVED = "saved_hours";
    
    protected $titles = array(self::CREATE_SPRINT => "New sprint created", self::EDIT_SPRINT => "Sprint edited", 
            self::FINALIZED_SPRINT => "Sprint finalized", self::STATE_HOURS_SAVED=>"Sprint saved with hours");
    protected $description = array(self::CREATE_SPRINT => "A new sprint has been created with name %s%",
                                self::EDIT_SPRINT=>"The sprint %s% has been edited",
                                self::FINALIZED_SPRINT => "The sprint %s% has been marked as finalized",
                                self::STATE_HOURS_SAVED => "The hours state of sprint %s% has been registered");
    /**
     * @ORM\ManyToOne(targetEntity="EasyScrumREST\SprintBundle\Entity\Sprint", inversedBy="actions")
     */
    private $sprint;

    public function getSprint()
    {
        return $this->sprint;
    }
    
    public function setSprint(Sprint $sprint)
    {
        $this->sprint = $sprint;
    }
    
    public function getIcon()
    {
        return 'fa-tasks';
    }
}