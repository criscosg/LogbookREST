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
        $today = new \DateTime('today');

        return $this->render('FrontendBundle:Frontend:home.html.twig', array('sprints'=>$sprints, 'today'=>$today->format('d/m')));
    }
    
    public function loginAction()
    {
        return $this->renderLoginTemplate('FrontendBundle:Commons:login.html.twig');
    }

}