<?php
namespace EasyScrumREST\FrontendBundle\Controller;

use EasyScrumREST\FrontendBundle\Controller\EasyScrumController;
use EasyScrumREST\FrontendBundle\Form\StatisticSearchType;
use EasyScrumREST\FrontendBundle\Util\StatisticSearchHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class FrontendController extends EasyScrumController
{
    public function homeAction()
    {
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
    
    public function statisticsAction(Request $request)
    {
        $company = $this->getUser()->getCompany()->getId();
        $search = new StatisticSearchHelper($company);
        $form = $this->createForm(new StatisticSearchType($company), $search);
        $form->handleRequest($request);
        list($focusCharts, $hoursData) = $this->get('sprint.focus_member')->getFocusFactorWhole($search);
        $projects = $this->get('statistics')->getTimeSpentByProject($search);
        $accuracy = $this->get('statistics')->getPlanificationAccuracy($search);
        $generalStatistics = $this->get('statistics')->getGeneralStatistics($search);

        return $this->render('FrontendBundle:Frontend:statistics.html.twig', array('focusMembers'=>$focusCharts, 'form' =>$form->createView(),
                'spentTimeProjects'=>$projects, 'accuracy'=>$accuracy, 'general'=>$generalStatistics, 'hoursData'=>$hoursData));
    }
}