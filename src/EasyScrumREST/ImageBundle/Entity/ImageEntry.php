<?php

namespace EasyScrumREST\ImageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use EasyScrumREST\ImageBundle\Util\FileHelper;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use EasyScrumREST\TaskBundle\Entity\Task;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="ImageTaskRepository")
 * @ExclusionPolicy("all")
 */
class ImageTask extends Image
{
    protected $subdirectory = "images/task";
    protected $maxImages = 100;

    /**
     * @var Task $task
     *
     * @ORM\ManyToOne(targetEntity="EasyScrumREST\TaskBundle\Entity\Task", inversedBy="images")
     */
    protected $task;

    public function getTask()
    {
        return $this->task;
    }

    public function setTask(Task $task)
    {
        $this->task = $task;
    }

    public function createCopies()
    {
        list($oldRoute, $copies) = parent::createCopies();
        if ($nav = $this->createNav()) {
            $copies[] = $nav;
        }

        return array($oldRoute, $copies);
    }
}
