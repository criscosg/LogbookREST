<?php
namespace EasyScrumREST\ActionBundle\Entity;

use EasyScrumREST\ProjectBundle\Entity\Backlog;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class ActionBacklog extends Action
{
    const CREATE_BACKLOG = "create_backlog";
    const EDIT_BACKLOG = "edited_backlog";
    const FINALIZED_BACKLOG = "finalized_backlog";
    const NEW_ISSUE = "create_issue";
    
    public $titles = array(self::CREATE_BACKLOG => "New backlog task created", self::EDIT_BACKLOG => "Backlog task edited", 
            self::FINALIZED_BACKLOG => "Backlog task finalized", self::NEW_ISSUE=>"A new issue added to backlog task");
    public $description = array(self::CREATE_BACKLOG => "A new backlog task has been created in project %p% with name %b%",
                                self::EDIT_BACKLOG=>"The backlog %b% has been edited in project %p%",
                                self::FINALIZED_BACKLOG => "The backlog %b% has been marked as finalized in project %p%",
                                self::NEW_ISSUE => "A new issue has been added to backlog task %b%");
    /**
     * @ORM\ManyToOne(targetEntity="EasyScrumREST\ProjectBundle\Entity\Backlog")
     */
    private $backlog;

    public function getBacklog()
    {
        return $this->backlog;
    }
    
    public function setBacklog(Backlog $backlog)
    {
        $this->backlog = $backlog;
    }
    
    public function getTitle()
    {
        return $titles[$this->getType()];
    }
    
    public function getDescription()
    {
        return $description[$this->getType()];
    }
    
    public function getIcon()
    {
        return 'fa-suitcase';
    }
}