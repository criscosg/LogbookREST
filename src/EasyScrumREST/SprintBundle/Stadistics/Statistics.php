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

    public function getGeneralStatistics($search)
    {
        $sprints = $this->em->getRepository('SprintBundle:Sprint')->findSprintsForStatistics($search);
        $averageFocus = 0;
        $averageDroppedTasks = 0;
        $focusProgression = array();
        foreach ($sprints as $sprint) {
            if($sprint->getHoursDone() > 0 && $sprint->getSpentHours() > 0) {
                $finalFocus = $sprint->getHoursDone() / $sprint->getSpentHours();
                $averageFocus += $finalFocus;
            } else {
                $finalFocus = 0;
            }
            $averageDroppedTasks += count($sprint->getTaskUndone());
            if(!array_key_exists($sprint->getDateTo()->getTimestamp()*1000, $focusProgression))
                $focusProgression[$sprint->getDateTo()->getTimestamp()*1000] = ($finalFocus) * 100;
            else
                $focusProgression[$sprint->getDateTo()->getTimestamp()*1000] += ($finalFocus) * 100;
        }
        ksort($focusProgression);
        if(count($sprints)>0) {
            $averageFocus = $averageFocus / count($sprints);
            $averageDroppedTasks = $averageDroppedTasks / count($sprints);
        } else {
            $averageFocus = 0;
            $averageDroppedTasks = 0;
        }
        
        $tasks = $this->em->getRepository('TaskBundle:Task')->findTasksForStatistics($search);
        $spenthours = 0;
        foreach ($tasks as $task) {
            $spenthours += $task->getHoursSpent();
        }
        
        $urgencies = $this->em->getRepository('TaskBundle:Urgency')->findUrgenciesForStatistics($search);
        $spenthoursUrgencies = 0;
        foreach ($urgencies as $urgency) {
            $spenthoursUrgencies += $urgency->getHoursSpent();
        }

        return array('averageFocus'=> intval($averageFocus*100), 'sprintsFinalized'=>count($sprints), 'droppedTasks'=>$averageDroppedTasks,
                'spentHours'=>$spenthours, 'spentHoursUrgency'=>$spenthoursUrgencies, 'focusProgression'=>$focusProgression);
    }

    public function getTimeSpentByProject($search)
    {
        $sprints = $this->em->getRepository('SprintBundle:Sprint')->findSprintsForStatistics($search);
        $chart = array();

        foreach ($sprints as $sprint) {
            if(!array_key_exists($sprint->getProject()->getTitle(), $chart))
                $chart[$sprint->getProject()->getTitle()]=0;
            $chart[$sprint->getProject()->getTitle()] += $sprint->getSpentHours();
        }

        return $chart;
    }

    public function getPlanificationAccuracy($search)
    {
        $tasks = $this->em->getRepository('TaskBundle:Task')->findTasksForStatistics($search);
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
