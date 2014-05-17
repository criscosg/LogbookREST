<?php
namespace EasyScrumREST\SynchronizeBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Options;

class SynchronizeController extends FOSRestController
{
    /**
     * POST Route annotation.
     * @Post("/synchronize")
     * @View(serializerEnableMaxDepthChecks=true)
     */
    public function synchronizeAction(Request $request)
    {
        if (!is_null($this->getUser()) && ($this->container->get('security.context')->isGranted('ROLE_USER'))) {
            return $this->container->get('synchronize')->synchronize($request->request->all(), $this->getUser());
        }

        return "No es un veterinario así que no puedes sincronizar";
    }

    /**
     * POST Route annotation.
     * @Post("/second-synchronize")
     * @View(serializerEnableMaxDepthChecks=true)
     */
    public function secondSynchronizeAction(Request $request)
    {
        if (!is_null($this->getUser()) && ($this->container->get('security.context')->isGranted('ROLE_VETERINARY'))) {
            return $this->container->get('synchronize.images')->synchronize($request->request->all(), $this->getUser());
        }
    
        return "No es un veterinario así que no puedes sincronizar";
    }
}