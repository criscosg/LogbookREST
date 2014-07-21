<?php

namespace EasyScrumREST\FrontendBundle\Controller;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class EasyScrumController extends Controller
{
    const CACHE_LONG_TIME = 86400;
    const CACHE_MEDIUM_TIME = 600;

    protected function resetToken($user, $provider = 'iventia_user')
    {
        $token = new UsernamePasswordToken($user, null, $provider, $user->getRoles());
        $this->container->get('security.context')->setToken($token);
        $this->container->get('session')->set("_security_private", serialize($token));
    }

    protected function getHttpJsonResponse($jsonResponse)
    {
        $response = new \Symfony\Component\HttpFoundation\Response($jsonResponse);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    protected function setTranslatedFlashMessage($message, $class = 'info')
    {
        $translatedMessage = $this->get('translator')->trans($message);
        $this->get('session')->getFlashBag()->set($class, $translatedMessage);
    }

    protected function renderLoginTemplate($template)
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR, $session->get(SecurityContext::AUTHENTICATION_ERROR));

        return $this->render($template, array(
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error' => $error));
    }

    protected function noPermission()
    {
        throw new \Symfony\Component\HttpKernel\Exception\HttpException(401);
    }
}