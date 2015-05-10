<?php

namespace EasyScrumREST\SprintBundle\Stadistics;
use EasyScrumREST\UserBundle\Entity\ApiUser;
use EasyScrumREST\SprintBundle\Entity\Sprint;
use EasyScrumREST\UserBundle\Handler\UserHandlerInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\BrowserKit\Request;

class Statistics
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getGeneralStatistics($company, $project)
    {
        if($project)
            $sprints = $this->em->getRepository('SprintBundle:Sprint')->findBy(array('finalized'=>true, 'company'=>$company, 'project'=>$project));
        else
            $sprints = $this->em->getRepository('SprintBundle:Sprint')->findBy(array('finalized'=>true, 'company'=>$company));

        $averageFocus = 0;
        $averageDroppedTasks = 0;
        $focusProgression = array();
        foreach ($sprints as $sprint) {
            $averageFocus += $sprint->getHoursDone() / $sprint->getSpentHours();
            $averageDroppedTasks += count($sprint->getTaskUndone());
            $focusProgression[$sprint->getDateTo()->getTimestamp()*1000] = ($sprint->getHoursDone() / $sprint->getSpentHours()) * 100;
        }
        ksort($focusProgression);
        $averageFocus = $averageFocus / count($sprints);
        $averageDroppedTasks = $averageDroppedTasks / count($sprints);
        
        $tasks = $this->em->getRepository('TaskBundle:Task')->findAllTasksCompany($company, $project);
        $spenthours = 0;
        foreach ($tasks as $task) {
            $spenthours += $task->getHoursSpent();
        }
        
        $urgencies = $this->em->getRepository('TaskBundle:Urgency')->findAllUrgenciesCompany($company, $project);
        $spenthoursUrgencies = 0;
        foreach ($urgencies as $urgency) {
            $spenthoursUrgencies += $urgency->getHoursSpent();
        }

        return array('averageFocus'=> intval($averageFocus*100), 'sprintsFinalized'=>count($sprints), 'droppedTasks'=>$averageDroppedTasks,
                'spentHours'=>$spenthours, 'spentHoursUrgency'=>$spenthoursUrgencies, 'focusProgression'=>$focusProgression);
    }

    public function getTimeSpentByProject($company)
    {
        $projects = $this->em->getRepository('ProjectBundle:Project')->findBy(array('company'=>$company));
        $chart = array();
        foreach ($projects as $project) {
            $chart[$project->getTitle()]=0;
            foreach ($project->getSprints() as $sprint) {
                $chart[$project->getTitle()] += $sprint->getSpentHours();               
            }
        }
        
        return $chart;
    }

    public function getPlanificationAccuracy($company)
    {
        $tasks = $this->em->getRepository('TaskBundle:Task')->findAllTasksCompany($company);
        $data = array('exact planification' => 0, 'took from 0 to 25% more time' => 0, 'took over 25% more time' => 0, 'took over 25% less time' => 0, 'took from 0 to 25% less time' => 0);
        foreach ($tasks as $task) {
            if($task->getHoursSpent() == $task->getHours()) {
                $data['exact planification']++;
            } elseif($task->getHoursSpent() > ($task->getHours() * 1.25)) {
                $data['took over 25% more time']++;
            } elseif($task->getHoursSpent() < ($task->getHours() - ($task->getHours() * 0.25))) {
                $data['took over 25% less time']++;
            } elseif($task->getHoursSpent() > $task->getHours()) {
                $data['took from 0 to 25% more time']++;
            }elseif($task->getHoursSpent() < $task->getHours()) {
                $data['took from 0 to 25% less time']++;
            }
        }

        return $data;
    }
}
