<?php

namespace EasyScrumREST\UserBundle\Statistics;

use EasyScrumREST\FrontendBundle\Util\StatisticSearchHelper;
use EasyScrumREST\UserBundle\Entity\ApiUser;
use EasyScrumREST\UserBundle\Handler\UserHandlerInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\BrowserKit\Request;

class UserStatistics
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getGeneralStatistics(ApiUser $user)
    {
        $averageFocus = 0;
        $tasks = $this->em->getRepository('TaskBundle:Task')->findTasksDoneByUser($user);
        $spenthours = 0;
        $usefulHours = 0;
        foreach ($tasks as $task) {
            $cont = 0;
            $hours = $task->getListHours();
            foreach ($hours as $hour) {
                if($user->isEqualTo($hour->getUser())) {
                    if($cont > 0){
                        if($hours[$cont-1]->getHoursEnd() <= $hour->getHoursEnd()){
                            $usefulHours+=0;
                            $spenthours += $hour->getHoursSpent();
                        } else {
                            $usefulHours += ($hours[$cont-1]->getHoursEnd() - $hour->getHoursEnd());
                            $spenthours += $hour->getHoursSpent();
                        }
                    } else {
                        if($hour->getTask()->getHours() <= $hour->getHoursEnd()){
                            $usefulHours+=0;
                            $spenthours += $hour->getHoursSpent();
                        } else {
                            $usefulHours += ($task->getHours() - $hour->getHoursEnd());
                            $spenthours += $hour->getHoursSpent();
                        }
                    }
                }
                $cont++;
            }
        }
        if($spenthours > 0 && $usefulHours > 0)
            $averageFocus = $usefulHours/$spenthours;

        $urgencies = $this->em->getRepository('TaskBundle:Urgency')->findUrgenciesForUser($user);
        $spenthoursUrgencies = 0;
        foreach ($urgencies as $urgency) {
            $spenthoursUrgencies += $urgency->getHoursSpent();
        }

        return array('average focus'=> intval($averageFocus*100), 'ended tasks'=>count($tasks),
                'spent hours'=>$spenthours, 'useful hours'=>$usefulHours,'spent hours urgency'=>$spenthoursUrgencies);
    }

    public function hoursSpentCharts(ApiUser $user)
    {
        $tasks = $this->em->getRepository('TaskBundle:Task')->findTasksDoneByUser($user);
        $date = new \DateTime($user->getCreated()->format('d-m-Y'));
        $to = new \DateTime('Today');
        $focusChart = array();
        while ($date <= $to) {
            $focusChart[$date->getTimestamp()*1000]['hours'] = 0;
            $focusChart[$date->getTimestamp()*1000]['hours_spent'] = 0;
            $date->modify('+1 day');
        }

        foreach ($tasks as $task) {
            $cont = 0;
            $hours = $task->getListHours()->toArray();
            foreach ($hours as $hour) {
                if($user->isEqualTo($hour->getUser())) {
                    $hourDate = new \DateTime($hour->getCreated()->format('d-m-Y'));
                    if($cont > 0){
                        if($hours[$cont-1]->getHoursEnd() <= $hour->getHoursEnd()){
                            $focusChart[$hourDate->getTimestamp()*1000]['hours']+=0;
                            $focusChart[$hourDate->getTimestamp()*1000]['hours_spent'] += $hour->getHoursSpent();
                        } else {
                            $focusChart[$hourDate->getTimestamp()*1000]['hours'] += $hours[$cont-1]->getHoursEnd() - $hour->getHoursEnd();
                            $focusChart[$hourDate->getTimestamp()*1000]['hours_spent'] += $hour->getHoursSpent();
                        }
                    } else {
                        if($hour->getTask()->getHours() <= $hour->getHoursEnd()){
                            $focusChart[$hourDate->getTimestamp()*1000]['hours']+=0;
                            $focusChart[$hourDate->getTimestamp()*1000]['hours_spent'] += $hour->getHoursSpent();
                        } else {
                            $focusChart[$hourDate->getTimestamp()*1000]['hours'] += $hour->getTask()->getHours() - $hour->getHoursEnd();
                            $focusChart[$hourDate->getTimestamp()*1000]['hours_spent'] += $hour->getHoursSpent();
                        }
                    }
                }
                $cont++;
            }
        }

        return $focusChart;
    }

    public function getPlanificationAccuracy(ApiUser $user)
    {
        $tasks = $this->em->getRepository('TaskBundle:Task')->findTasksForUser($user);
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
