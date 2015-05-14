<?php

namespace EasyScrumREST\SprintBundle\Stadistics;
use EasyScrumREST\UserBundle\Entity\ApiUser;
use EasyScrumREST\SprintBundle\Entity\Sprint;
use EasyScrumREST\UserBundle\Handler\UserHandlerInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\BrowserKit\Request;

class FocusFactorMembers
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getChartFocusMember(Sprint $sprint)
    {
        $from = new \DateTime($sprint->getDateFrom()->format('Y/m/d'));
        $date = clone $sprint->getDateFrom();
        $to = clone $sprint->getDateTo();
        $focusChart = array();
        $chartData = array();
        while ($date <= $to) {
            $day=$date->format('l');
            foreach ($sprint->getTasks() as $task) {
                $hours=$this->em->getRepository('TaskBundle:HoursSpent')->getHoursSpentByUser($task, $date);
                $cont = 0;
                foreach ($hours as $hour) {
                    if($cont > 0){
                        if($hours[$cont-1]->getHoursEnd() <= $hour->getHoursEnd()){
                            $focusChart[$hour->getUser()->getId()][$date->format('d/m')]['hours'][]=0;
                            $focusChart[$hour->getUser()->getId()][$date->format('d/m')]['hours_spent'][] = $hour->getHoursSpent();
                        } else {
                            $focusChart[$hour->getUser()->getId()][$date->format('d/m')]['hours'][] = $hours[$cont-1]->getHoursEnd() - $hour->getHoursEnd();
                            $focusChart[$hour->getUser()->getId()][$date->format('d/m')]['hours_spent'][] = $hour->getHoursSpent();
                        }
                    } else {
                        if($hour->getTask()->getHours() <= $hour->getHoursEnd()){
                            $focusChart[$hour->getUser()->getId()][$date->format('d/m')]['hours'][]=0;
                            $focusChart[$hour->getUser()->getId()][$date->format('d/m')]['hours_spent'][] = $hour->getHoursSpent();
                        } else {
                            $focusChart[$hour->getUser()->getId()][$date->format('d/m')]['hours'][] = $hour->getTask()->getHours() - $hour->getHoursEnd();
                            $focusChart[$hour->getUser()->getId()][$date->format('d/m')]['hours_spent'][] = $hour->getHoursSpent();
                        }
                    }
                    $cont++;
                }
            }
            $date->modify('+1 day');
        }

        while ($from <= $to) {
            $day=$from->format('l');
            foreach ($focusChart as $key => $userChart){
                if(isset($userChart[$from->format('d/m')])){
                    $totalFocus=0;
                    $totalHours=0;
                    $totalSpent=0;
                    foreach ($userChart[$from->format('d/m')]['hours'] as $hours){
                        $totalHours += $hours;
                    }
                    foreach ($userChart[$from->format('d/m')]['hours_spent'] as $hoursSpent) {
                        $totalSpent += $hoursSpent;
                    }
                    if($totalHours>0 && $totalSpent>0)
                        $totalFocus = $totalHours/$totalSpent;
                    $chartData[$key][$from->format('d/m')] = $totalFocus;
                } else {
                    $chartData[$key][$from->format('d/m')] = 0;
                }
            }
            $from->modify('+1 day');
        }

        return $chartData;
    }
    
    public function getFocusFactorWhole($search)
    {
        $sprints = $this->em->getRepository('SprintBundle:Sprint')->findSprintsForStatistics($search);
        $focusChart = array();
        $chartData = array();
        foreach ($sprints as $sprint) {
            foreach ($sprint->getTasks() as $task) {
                $cont = 0;
                $hours = $task->getListHours();
                foreach ($hours as $hour) {
                    if(!array_key_exists($hour->getUser()->__toString(), $focusChart)) {
                        $focusChart[$hour->getUser()->__toString()]['hours'] = 0;
                        $focusChart[$hour->getUser()->__toString()]['hours_spent'] = 0;
                    }

                    if($cont > 0){
                        if($hours[$cont-1]->getHoursEnd() <= $hour->getHoursEnd()){
                            $focusChart[$hour->getUser()->__toString()]['hours']+=0;
                            $focusChart[$hour->getUser()->__toString()]['hours_spent'] += $hour->getHoursSpent();
                        } else {
                            $focusChart[$hour->getUser()->__toString()]['hours'] += ($hours[$cont-1]->getHoursEnd() - $hour->getHoursEnd());
                            $focusChart[$hour->getUser()->__toString()]['hours_spent'] += $hour->getHoursSpent();
                        }
                    } else {
                        if($hour->getTask()->getHours() <= $hour->getHoursEnd()){
                            $focusChart[$hour->getUser()->__toString()]['hours']+=0;
                            $focusChart[$hour->getUser()->__toString()]['hours_spent'] += $hour->getHoursSpent();
                        } else {
                            $focusChart[$hour->getUser()->__toString()]['hours'] += ($task->getHours() - $hour->getHoursEnd());
                            $focusChart[$hour->getUser()->__toString()]['hours_spent'] += $hour->getHoursSpent();
                        }
                    }
                    $cont++;
                }
            }
        }

        foreach ($focusChart as $key => $userChart){
            if(isset($userChart['hours']) && isset($userChart['hours_spent'])){
                $totalFocus=0;
                if($userChart['hours'] > 0 && $userChart['hours_spent'] > 0) {
                    $totalFocus = $userChart['hours']/$userChart['hours_spent'];
                }
                $chartData[$key] = $totalFocus*100;
            } else {
                $chartData[$key] = 0;
            }
        }

        return array($chartData, $focusChart);
    }

}