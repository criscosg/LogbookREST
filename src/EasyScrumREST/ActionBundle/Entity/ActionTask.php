<?php
namespace EasyScrumREST\ActionBundle\Entity;

use EasyScrumREST\TaskBundle\Entity\Task;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class ActionTask extends Action
{
    const CREATE_TASK = "create_task";
    const DROPED_TASK = "droped_task";
    const DONE_TASK = "done_task";
    const TODO_TASK = "todo_task";
    const ONPROCESS_TASK = "onprocess_task";
    const BLOCKED_TASK = "blocked_task";
    const UPDATED_TASK_HOURS = "updated_task_hours";
    
    public $titles = array(self::CREATE_TASK => "A new Task has been created", self::DROPED_TASK => "A task has dropped from a sprint",
                           self::DONE_TASK => "A task has been moved to done section", self::TODO_TASK => "A task has been moved to TODO section",
                           self::ONPROCESS_TASK => "A task has been moved to on process section", self::BLOCKED_TASK => "A user has blocked a task",
                           self::UPDATED_TASK_HOURS => "A user has updated the hours of a task");
    public $description = array(self::CREATE_TASK=>"The task %t% has been created in sprint %s%",
                            self::DROPED_TASK=>"The task %t% has been dropped from sprint %s%",
                            self::DONE_TASK=>"The task %t% has been moved to DONE in sprint %s%",
                            self::TODO_TASK => "The task %t% has been moved to TODO in sprint %s%",
                            self::ONPROCESS_TASK=> "The task %t% has been moved to ON PROCESS in sprint %s%",
                            self::BLOCKED_TASK => "The task %t% has been blocked", self::UPDATED_TASK_HOURS=>"The hours spent of task %t% has been updated");
    
    /**
     * @ORM\ManyToOne(targetEntity="EasyScrumREST\TaskBundle\Entity\Task")
     */
    private $task;

    public function getTask()
    {
        return $this->task;
    }
    
    public function setTask(Task $task)
    {
        $this->task = $task;
    }
    
    public function getIcon()
    {
        return 'fa-wrench';
    }
    
}