<?php
namespace EasyScrumREST\UserBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
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

class PasswordController extends FOSRestController{

    /**
    * @param Request $request the request object
    *
    * @return FormTypeInterface|View
    */
    public function postChangepaAction(Request $request)
    {
        try {
            $newRecoveryPassword = $this->get('password.handler')->post($request);
            $user = $this->getDoctrine()->getEntityManager()->getRepository('UserBundle:User')->findOneByEmail($newRecoveryPassword->getEmail());
            $userEvent = new UserEvent($newRecoveryPassword);
            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->dispatch(UserEvents::RECOVER_PASSWORD, $userEvent);

            $routeOptions = array(
                    'salt' => $newRecoveryPassword->getSalt(),
                    '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('get_password', $routeOptions, Codes::HTTP_CREATED);
        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
    }
    
    /**
     *
     * @View()
     *
     * @return FormTypeInterface
     */
    public function newChangepaAction()
    {
        return $this->createForm(new VeterinaryType());
    }

    /**
     * @param Request $request
     *
     * @View()
     * @return array
     */
    public function getChangepaAction($salt)
    {
        $user = $this->container->get('password.handler')->get($salt);

        return $user;
    }

    /**
     * Fetch the Page or throw a 404 exception.
     *
     * @param mixed $id
     *
     * @return PageInterface
     *
     * @throws NotFoundHttpException
     */
    protected function getOr404($email)
    {
        if (!($page = $this->container->get('user.handler')->get(null, $email))) {
            throw new NotFoundHttpException(sprintf('The User \'%s\' was not found.', $email));
        }

        return $page;
    }

    /**
     *
     * @View()
     *
     * @param Request $request
     * @param int     $id
     *
     * @return View
     *
     * @throws NotFoundHttpException when veterinary not exist
     */
    public function putChangepaAction(Request $request)
    {
        try {
            if (($recoverPass = $this->container->get('password.handler')->get($request->get('salt')))) {
                $statusCode = Codes::HTTP_CREATED;
                if ($user = $this->container->get('user.handler')->get(null, $recoverPass->getEmail())) {
                    $statusCode = Codes::HTTP_CREATED;
                    $newUser = $this->container->get('api_user.handler')->patch($user, $request);
                    $response = new Response('Der Tierartz wurde erfolgreich gespeichert', $statusCode);

                    return $response;
                }
            } else {
                $statusCode = Codes::HTTP_CREATED;
                $response = new Response('The User \'%s\' was not found ', $statusCode);
            }

            return $response;
        } catch (\Exception $exception) {

            return $exception->getMessage();
        }
    }
}