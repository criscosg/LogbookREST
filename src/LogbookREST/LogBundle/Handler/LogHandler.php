<?php

namespace LogbookREST\LogBundle\Handler;
use LogbookREST\LogBundle\Entity\Log;
use LogbookREST\LogBundle\Form\LogType;
use LogbookREST\UserBundle\Handler\UserHandlerInterface;
use Doctrine\ORM\EntityManager;
use LogbookREST\UserBundle\Entity\AdminUser;
use Symfony\Component\Form\FormFactoryInterface;
use LogbookREST\UserBundle\Form\AdminUserType;
use Symfony\Component\BrowserKit\Request;

class LogHandler
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
        return $this->em->getRepository('LogBundle:Log')->find($id);
    }

    /**
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 20, $offset = 0, $orderby = null, $search=null)
    {

        return $this->em->getRepository('LogBundle:Log')->findLogBySearch($search, $orderby, $limit, $offset);
    }

    /**
     * Create a new Log.
     *
     * @param $request
     *
     * @return Log
     */
    public function post($request)
    {
        $log = new Log();

        return $this->processForm($log, $request, 'POST');
    }
    
    /**
     * @param Log $log
     * @param $request
     *
     * @return Log
     */
    public function put(Log $entity, $request)
    {
        return $this->processForm($entity, $request);
    }
    
    /**
     * @param Log $log
     * @param $request
     *
     * @return Log
     */
    public function patch(Log $entity, $request)
    {
        return $this->processForm($entity, $request, 'PATCH');
    }
    
    /**
     * @param Log $log
     *
     * @return Log
     */
    public function delete(Log $entity)
    {
        $time = new \DateTime('now');
        $entity->setDeleted($time);
        foreach ($entity->getHorses() as $horse) {
            $horse->setDeleted($time);
            $this->em->persist($horse);
            $this->em->flush($horse);
        }
        $this->em->persist($entity);
        $this->em->flush($entity);
    }
    
    /**
     * Processes the form.
     *
     * @param Log     $log
     * @param array         $parameters
     * @param String        $method
     *
     * @return Log
     *
     * @throws \Exception
     */
    private function processForm(Log $entity, $request, $method = "PUT")
    {
        $form = $this->factory->create(new LogType(), $entity, array('method' => $method));
        $form->handleRequest($request);
        if (!$form->getErrors()) {
            $log = $form->getData();
            $this->em->persist($log);
            $this->em->flush($log);

            return $log;
        }

        throw new \Exception('Invalid submitted data');
    }
}