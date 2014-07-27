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

        return $this->render('FrontendBundle:Frontend:example.html.twig');
    }
    
    public function loginAction()
    {
        return $this->renderLoginTemplate('FrontendBundle:Commons:login.html.twig');
    }

}