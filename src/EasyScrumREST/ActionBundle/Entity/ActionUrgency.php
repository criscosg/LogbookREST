<?php
namespace EasyScrumREST\ActionBundle\Entity;

use EasyScrumREST\TaskBundle\Entity\Urgency;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class ActionUrgency extends Action
{
    const CREATE_URGENCY = "create_urgency";
    const DROPED_URGENCY = "droped_urgency";
    const DONE_URGENCY = "done_urgency";
    const TODO_URGENCY = "todo_urgency";
    const ONPROCESS_URGENCY = "onprocess_urgency";
    const BLOCKED_URGENCY = "blocked_urgency";
    const UPDATED_URGENCY_HOURS = "updated_urgency_hours";
    
    public $titles = array(self::CREATE_URGENCY => "A new urgency has been created", self::DROPED_URGENCY => "A urgency has dropped from a sprint",
                           self::DONE_URGENCY => "A urgency has been moved to done section", self::TODO_URGENCY => "A urgency has been moved to TODO section",
                           self::ONPROCESS_URGENCY => "A urgency has been moved to on process section", self::BLOCKED_URGENCY => "A user has blocked a urgency",
                           self::UPDATED_URGENCY_HOURS => "A user has updated the hours of a urgency");
    public $description = array(self::CREATE_URGENCY=>"The urgency %u% has been created",
                            self::DROPED_URGENCY=>"The urgency %u% has been dropped",
                            self::DONE_URGENCY=>"The urgency %u% has been moved to DONE",
                            self::TODO_URGENCY => "The urgency %u% has been moved to TODO",
                            self::ONPROCESS_URGENCY=> "The urgency %u% has been moved to ON PROCESS",
                            self::BLOCKED_URGENCY => "The urgency %u% has been blocked", self::UPDATED_URGENCY_HOURS=>"The hours spent of urgency %u% has been updated");
    
    /**
     * @ORM\ManyToOne(targetEntity="EasyScrumREST\TaskBundle\Entity\Urgency")
     */
    private $urgency;

    public function getUrgency()
    {
        return $this->urgency;
    }
    
    public function setUrgency(Urgency $urgency)
    {
        $this->urgency = $urgency;
    }
    
    public function getIcon()
    {
        return 'fa-fire';
    }
    
}