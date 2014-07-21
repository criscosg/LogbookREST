<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new EasyScrumREST\UserBundle\UserBundle(),
            new EasyScrumREST\TestBundle\TestBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new RaulFraile\Bundle\LadybugBundle\RaulFraileLadybugBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Nelmio\ApiDocBundle\NelmioApiDocBundle(),
            new EasyScrumREST\TaskBundle\TaskBundle(),
            new EasyScrumREST\SprintBundle\SprintBundle(),
            new FOS\OAuthServerBundle\FOSOAuthServerBundle(),
            new EasyScrumREST\OAuthBundle\OAuthBundle(),
	    new EasyScrumREST\SynchronizeBundle\SynchronizeBundle(),
            new EasyScrumREST\MessageBundle\MessageBundle(),
            new EasyScrumREST\FrontendBundle\FrontendBundle(),
            new EasyScrumREST\ProjectBundle\ProjectBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
