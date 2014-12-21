<?php

namespace EasyScrumREST\ActionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ActionController extends Controller
{
    public function indexAction()
    {
        $actions = $this->getDoctrine()->getRepository('ActionBundle:Action')->companyActions($this->getUser());
        
        return $this->render('ActionBundle:Action:index.html.twig', array('actions' => $actions));
    }
}
