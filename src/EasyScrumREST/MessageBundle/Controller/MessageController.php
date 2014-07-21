<?php

namespace EasyScrumREST\MessageBundle\Controller;

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

class MessageController extends FOSRestController
{
    /**
     * @QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing pages.")
     * @QueryParam(name="limit", requirements="\d+", nullable=true, default="20", description="How many pages to return.")
     * @View(serializerEnableMaxDepthChecks=true)
     *
     * @param Request               $request      the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getMessagesAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');
        $company=null;
        if (!is_null($this->getUser()) && ($this->container->get('security.context')->isGranted('ROLE_API_USER'))) {
            $company=$this->getUser()->getCompany()->getId();
        }

        $messages = $this->container->get('message.handler')->all($limit, $offset, $company);

        return array_reverse($messages);
    }
    
    /**
     * @param $id
     *
     * @View()
     * @return array
     */
    public function getMessageAction($id)
    {
        $message = $this->getOr404($id);
    
        return $message;
    }
    
    /**
     * @View(template = "MessageBundle:Message:new-message.html.twig", statusCode = Codes::HTTP_BAD_REQUEST, templateVar = "form")
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     */
    public function postMessageAction(Request $request)
    {
        try {
            if (!is_null($this->getUser()) && ($this->container->get('security.context')->isGranted('ROLE_API_USER'))) {
                $newMessage = $this->get('message.handler')->post($request, $this->getUser());
                
                $routeOptions = array(
                        'id' => $newMessage->getId(),
                        '_format' => $request->get('_format')
                );
                
                return $this->routeRedirectView('get_message', $routeOptions, Codes::HTTP_CREATED);
            }

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
    public function newMessageAction()
    {
        return $this->createForm(new MessageType());
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
     * @throws NotFoundHttpException when message not exist
     */
    public function putMessageAction(Request $request, $id)
    {
        try {
            if (!($message = $this->container->get('message.handler')->get($id))) {
                $statusCode = Codes::HTTP_CREATED;
                $message = $this->container->get('message.handler')->post($request);
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
                $message = $this->container->get('message.handler')->put($message, $request);
            }
            $response = new Response('Der Besitzer des Pferdes wurde erfolgreich gespeichert', $statusCode);
    
            return $response;
        } catch (\Exception $exception) {
    
            return $exception->getMessage();
        }
    }
    
    /**
     *
     * @View()
     *
     * @param Request $request
     * @param int     $id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when message not exist
     */
    public function patchMessageAction(Request $request, $id)
    {
        try {
            if (($message = $this->container->get('message.handler')->get($id))) {
                $statusCode = Codes::HTTP_ACCEPTED;
                $message = $this->container->get('message.handler')->patch($message, $request);
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
            }
            $response = new Response('Der Besitzer des Pferdes wurde erfolgreich gespeichert', $statusCode);
    
            return $response;
        } catch (NotFoundHttpException $exception) {
    
            return $exception->getMessage();
        }
    }
    
    /**
     *
     * @View()
     *
     * @param Request $request
     * @param int     $id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when message not exist
     */
    public function deleteMessageAction(Request $request, $id)
    {
        if (($message = $this->container->get('message.handler')->get($id))) {
            $statusCode = Codes::HTTP_ACCEPTED;
            $message = $this->container->get('message.handler')->delete($message);
        } else {
            $statusCode = Codes::HTTP_NO_CONTENT;
        }
        $response = new Response('Der Besitzer des Pferdes wurde erfolgreich gelöscht', $statusCode);
    
        return $response;
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
    protected function getOr404($id)
    {
        if (!($page = $this->container->get('message.handler')->get($id))) {
            throw new NotFoundHttpException(messagef('The Message \'%s\' was not found.',$id));
        }
    
        return $page;
    }
}