<?php

namespace EasyScrumREST\ActionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ActionController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ActionBundle:Action:index.html.twig', array('actions' => $actions));
    }
}
