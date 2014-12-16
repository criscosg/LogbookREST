<?php

namespace EasyScrumREST\ActionBundle\Entity;

use EasyScrumREST\ProjectBundle\Entity\Project;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class ActionProject extends Action
{
    const CREATE_PROJECT = "create_project";
    const EDIT_PROJECT = "edited_project";
    
    public $titles = array(self::CREATE_PROJECT => "New project created", self::EDIT_PROJECT => "Project edited");
    public $description = array(self::CREATE_PROJECT => "A new project has been created with name %p%",
            self::EDIT_PROJECT=>"The project %p% has been edited");
    
    /**
     * @ORM\ManyToOne(targetEntity="EasyScrumREST\ProjectBundle\Entity\Project")
     */
    private $project;

    public function getProject()
    {
        return $this->project;
    }

    public function setProject(Project $project)
    {
        $this->project = $project;
    }
    
    public function getIcon()
    {
        return 'fa-suitcase';
    }
}