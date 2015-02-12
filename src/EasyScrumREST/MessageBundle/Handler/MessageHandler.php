<?php

namespace EasyScrumREST\MessageBundle\Handler;
use EasyScrumREST\MessageBundle\Entity\Message;
use EasyScrumREST\MessageBundle\Form\MessageType;
use EasyScrumREST\UserBundle\Entity\ApiUser;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\BrowserKit\Request;

class MessageHandler
{
    private $em;
    private $factory;
    
    public function __construct(EntityManager $em, FormFactoryInterface $formFactory)
    {
        $this->em = $em;
        $this->factory = $formFactory;
    }

    public function get($id)
    {
        return $this->em->getRepository('MessageBundle:Message')->find($id);
    }

    /**
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 20, $offset = 0, $company=null)
    {
        return $this->em->getRepository('MessageBundle:Message')->findMessageBySearch($limit, $offset, $company);
    }
    
    /**
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function lastSecondsMessages($company)
    {
        return $this->em->getRepository('MessageBundle:Message')->findMessageInLastSeconds($company);
    }

    /**
     * Create a new Message.
     *
     * @param $request
     *
     * @return Message
     */
    public function post($request, ApiUser $user)
    {
        $message = new Message();
        $message->setUser($user);

        return $this->processForm($message, $request, 'POST');
    }
    
    /**
     * @param Message $message
     * @param $request
     *
     * @return Message
     */
    public function put(Message $entity, $request)
    {
        return $this->processForm($entity, $request);
    }
    
    /**
     * @param Message $message
     * @param $request
     *
     * @return Message
     */
    public function patch(Message $entity, $request)
    {
        return $this->processForm($entity, $request, 'PATCH');
    }
    
    /**
     * @param Message $message
     *
     * @return Message
     */
    public function delete(Message $entity)
    {
        $this->em->remove($entity);
        $this->em->flush($entity);
    }
    
    /**
     * Processes the form.
     *
     * @param Message     $message
     * @param array         $parameters
     * @param String        $method
     *
     * @return Message
     *
     * @throws \Exception
     */
    private function processForm(Message $entity, $request, $method = "PUT")
    {
        $form = $this->factory->create(new MessageType(), $entity, array('method' => $method));
        $form->handleRequest($request);
        if (!$form->getErrors()) {
            $this->em->persist($entity);
            $this->em->flush($entity);

            return $entity;
        }

        throw new \Exception('Invalid submitted data');
    }
}
