<?php

namespace EasyScrumREST\ActionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="ActionRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="subclass", type="string")
 * @ORM\DiscriminatorMap({"action_sprint"="ActionSprint", "project_action"="ActionProject", "action_task"="ActionTask"
 *                         ,"action_urgency"="ActionUrgency", "action_backlog"="ActionBacklog" })
 */
abstract class Action
{
    protected $titles = array();
    protected $description = array();
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
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    protected $type;
    
    /**
     * @ORM\ManyToOne(targetEntity="EasyScrumREST\UserBundle\Entity\ApiUser")
     */
    private $user;
    
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
    
    public function getType()
    {
        return $this->type;
    }
    
    public function setType($type)
    {
        $this->type = $type;
    }
    
    public function getUser()
    {
        return $this->user;
    }
    
    public function setUser($user)
    {
        $this->user = $user;
    }
    
    public function getTitleText()
    {
        return $this->titles[$this->getType()];
    }
    
    public function getDescriptionText()
    {
        return $this->description[$this->getType()];
    }
}