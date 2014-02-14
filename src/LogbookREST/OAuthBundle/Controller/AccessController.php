<?php

namespace LogbookREST\OAuthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class AccessController extends Controller
{
    public function loginAction(Request $request)
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } elseif (null !== $session && $session->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        if ($error) {
            $error = $error->getMessage(
            ); // WARNING! Symfony source code identifies this line as a potential security threat.
        }

        $lastUsername = (null === $session) ? '' : $session->get(SecurityContext::LAST_USERNAME);

        return $this->render('OAuthBundle:Access:login.html.twig', array(
                        'last_username' => $lastUsername,
                        'error' => $error));
    }

}
