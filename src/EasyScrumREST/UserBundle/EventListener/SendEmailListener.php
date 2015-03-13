<?php

namespace EasyScrumREST\UserBundle\EventListener;

use EasyScrumREST\UserBundle\Event\UserEvent;
use EasyScrumREST\UserBundle\Entity\RecoverPassword;
use EasyScrumREST\UserBundle\Event\UserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Routing\Router;

class SendEmailListener implements EventSubscriberInterface
{
    private $mailer;
    private $templating;
    private $router;
    private $noreply;

    public static function getSubscribedEvents()
    {
        return array(
            UserEvents::RECOVER_PASSWORD => 'onRecoverPassword');
    }

    public function __construct(\Swift_Mailer $mailer, EngineInterface $templating, Router $router, $noreply)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->router = $router;
        $this->noreply = $noreply;
    }

    public function onRecoverPassword(UserEvent $event)
    {
        $this->sendRecoveryPassEmail($event->getRecover());
    }

    private function sendRecoveryPassEmail(RecoverPassword $recover)
    {
        $url=$this->router->generate('change_password', array('salt'=>$recover->getSalt()), true);
        $from = $this->noreply;
        $to = $recover->getEmail();
        $messageBody = $this->templating->render('UserBundle:Email:emailForgetPassword.html.twig',
            array('url' => $url));
        $message = \Swift_Message::newInstance()
            ->setSubject('Restablish password EasyScrum')
            ->setFrom($from)
            ->setTo($to)
            ->setBody($messageBody, 'text/html');
        if (!$this->mailer->send($message)) {
            throw new \Exception("Error en el envío del correo para olvido de contraseña");
        }
    }

}
