<?php

namespace EasyScrumREST\ActionBundle\Manager;

use EasyScrumREST\ActionBundle\Entity\ActionProject;
use EasyScrumREST\ActionBundle\Entity\ActionBacklog;
use EasyScrumREST\ActionBundle\Entity\ActionUrgency;
use EasyScrumREST\ActionBundle\Entity\ActionTask;
use EasyScrumREST\ActionBundle\Entity\ActionSprint;
use Doctrine\ORM\EntityManager;

class ActionManager {

    private $em;
    
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function getLastUpdates($user)
    {
        $actions = $this->em->getRepository('ActionBundle:Action')->lastActions($user);
        
        return $actions;
    }
    
    public function createSprintAction($sprint, $user, $type){
        $actionSprint = new ActionSprint();
        $actionSprint->setSprint($sprint);
        $actionSprint->setType($type);
        $actionSprint->setUser($user);
        $this->em->persist($actionSprint);
        $this->em->flush();
    }
    
    public function createTaskAction($task, $user, $type){
        $actionTask = new ActionTask();
        $actionTask->setTask($task);
        $actionTask->setType($type);
        $actionTask->setUser($user);
        $this->em->persist($actionTask);
        $this->em->flush();
    }
    
    public function createUrgencyAction($urgency, $user, $type){
        $actionUrgency = new ActionUrgency();
        $actionUrgency->setUrgency($urgency);
        $actionUrgency->setType($type);
        $actionUrgency->setUser($user);
        $this->em->persist($actionUrgency);
        $this->em->flush();
    }
    
    public function createProjectAction($project, $user, $type){
        $actionProject = new ActionProject();
        $actionProject->setProject($project);
        $actionProject->setType($type);
        $actionProject->setUser($user);
        $this->em->persist($actionProject);
        $this->em->flush();
    }
    
    public function createBacklogAction($backlog, $user, $type){
        $actionBacklog = new ActionBacklog();
        $actionBacklog->setBacklog($backlog);
        $actionBacklog->setType($type);
        $actionBacklog->setUser($user);
        $this->em->persist($actionBacklog);
        $this->em->flush();
    }
}