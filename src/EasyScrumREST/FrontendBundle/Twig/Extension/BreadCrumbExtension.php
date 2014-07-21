<?php

namespace EasyScrumREST\FrontendBundle\Twig\Extension;

use EasyScrumREST\FrontendBundle\Twig\Extension\EasyScrumExtension;

class BreadCrumbExtension extends EasyScrumExtension
{

    protected $crumbs = array();

    public function getFunctions()
    {
        return array('setCrumbs' => new \Twig_Function_Method($this, 'setCrumbs'),
                     'getCrumbs' => new \Twig_Function_Method($this, 'getCrumbs'));
    }

    public function setCrumbs($links = array())
    {
        if(!empty($links)){
            array_push($this->crumbs, $links);
        }
    }

    public function getCrumbs()
    {
        return array_shift($this->crumbs);
    }    

    public function getName()
    {
        return 'breadcrumb_extension';
    }
    
}
