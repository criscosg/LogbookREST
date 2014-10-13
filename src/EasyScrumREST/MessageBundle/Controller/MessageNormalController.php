<?php
namespace EasyScrumREST\MessageBundle\Controller;

use EasyScrumREST\MessageBundle\Form\MessageType;

use EasyScrumREST\FrontendBundle\Controller\EasyScrumController;
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

class MessageNormalController extends EasyScrumController
{

    public function getMessagesAction(Request $request)
    {
        $company=$this->getUser()->getCompany()->getId();
        $messages = $this->container->get('message.handler')->all(50, 0, $company);
        $form=$this->createForm(new MessageType());

        return $this->render('MessageBundle:Message:view.html.twig', array('messages' => array_reverse($messages), 'form'=>$form->createView()));
    }

    /**
     * @Template("MessageBundle:Message:messages.html.twig")
     *
     * @return array
     */
    public function getMessagesAsyncAction(Request $request)
    {
        $company=$this->getUser()->getCompany()->getId();
        $messages = $this->container->get('message.handler')->lastSecondsMessages($company);

        return array('messages' => array_reverse($messages));
    }

    public function sendMessageAction(Request $request)
    {
        try {
            $newMessage = $this->get('message.handler')->post($request, $this->getUser());
            $html=$this->renderView('MessageBundle:Message:messages.html.twig',array('messages'=>array($newMessage)));
            $jsonResponse = json_encode(array('ok'=>true, 'message' => $html));
        } catch (InvalidFormException $exception) {
            $jsonResponse = json_encode(array('ok'=>false));
        }

        return $this->getHttpJsonResponse($jsonResponse);
    }
}