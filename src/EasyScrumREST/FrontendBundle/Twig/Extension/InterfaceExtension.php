<?php

namespace EasyScrumREST\FrontendBundle\Twig\Extension;

use EasyScrumREST\FrontendBundle\Twig\Extension\EasyScrumExtension;
use Symfony\Component\DependencyInjection\ContainerInterface;

class InterfaceExtension extends EasyScrumExtension
{

    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array('staticCall' => new \Twig_Function_Method($this, 'staticCall'),
                     'getBrowser' => new \Twig_Function_Method($this, 'getUserAgent')
        );
    }

    public function staticCall($class, $function, $args = array())
    {
        if (class_exists($class) && method_exists($class, $function)) {
            return call_user_func_array(array($class, $function), $args);
        }

        return null;
    }

    public function getUserAgent()
    {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $useragent = $_SERVER['HTTP_USER_AGENT'];

            $browsers = array(
                  'Internet Explorer 8' => '(MSIE 8\.[0-9]+)',
                  'Internet Explorer 7' => '(MSIE 7\.[0-9]+)',
                  'Internet Explorer 6' => '(MSIE 6\.[0-9]+)'
            );

            foreach ($browsers as $browser => $pattern) {
                   if (preg_match('/'.$pattern.'/i', $useragent)) {
                       return $browser;
                   }
            }
        }

        return null;
    }

    public function getName()
    {
        return 'interface_extension';
    }
}
