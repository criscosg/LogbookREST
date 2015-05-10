<?php
namespace EasyScrumREST\FrontendBundle\Controller;

use EasyScrumREST\FrontendBundle\Controller\EasyScrumController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class FrontendController extends EasyScrumController
{
    public function homeAction()
    {
        $request=$this->getRequest();
        $session = $request->getSession();
        $sprints = $this->get('sprint.handler')->getActiveSprints($this->getUser()->getCompany()->getId());
        $actions = $this->get('action.manager')->getLastUpdates($this->getUser());
        $today = new \DateTime('today');

        return $this->render('FrontendBundle:Frontend:home.html.twig', array('sprints'=>$sprints, 'today'=>$today->format('d/m'), 'actions'=> $actions));
    }
    
    public function loginAction()
    {
        return $this->renderLoginTemplate('FrontendBundle:Commons:login.html.twig');
    }

    public function calendarAction()
    {
        $sprints = $this->get('sprint.handler')->all(20, 0, null, array('company'=>$this->getUser()->getCompany()->getId()));
        
        return $this->render('FrontendBundle:Frontend:calendar.html.twig', array('sprints'=>$sprints));
    }
    
    public function statisticsAction($project = null)
    {
        $company = $this->getUser()->getCompany()->getId();
        list($focusCharts, $hoursData) = $this->get('sprint.focus_member')->getFocusFactorWhole($company, $project);
        $projects = $this->get('statistics')->getTimeSpentByProject($company);
        $accuracy = $this->get('statistics')->getPlanificationAccuracy($company);
        $generalStatistics = $this->get('statistics')->getGeneralStatistics($company, $project);

        return $this->render('FrontendBundle:Frontend:statistics.html.twig', array('focusMembers'=>$focusCharts,
                'spentTimeProjects'=>$projects, 'accuracy'=>$accuracy, 'general'=>$generalStatistics, 'hoursData'=>$hoursData));
    }
}