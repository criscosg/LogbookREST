<?php

namespace EasyScrumREST\SprintBundle\Handler;
use EasyScrumREST\SprintBundle\Entity\Sprint;
use EasyScrumREST\SprintBundle\Form\SprintType;
use EasyScrumREST\UserBundle\Handler\UserHandlerInterface;
use Doctrine\ORM\EntityManager;
use EasyScrumREST\UserBundle\Entity\AdminUser;
use Symfony\Component\Form\FormFactoryInterface;
use EasyScrumREST\UserBundle\Form\AdminUserType;
use Symfony\Component\BrowserKit\Request;

class SprintHandler
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
        return $this->em->getRepository('SprintBundle:Sprint')->find($id);
    }

    /**
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 20, $offset = 0, $orderby = null, $search=null)
    {

        return $this->em->getRepository('SprintBundle:Sprint')->findSprintBySearch($search, $orderby, $limit, $offset);
    }

    /**
     * Create a new Sprint.
     *
     * @param $request
     *
     * @return Sprint
     */
    public function post($request)
    {
        $sprint = new Sprint();

        return $this->processForm($sprint, $request, 'POST');
    }
    
    /**
     * @param Sprint $sprint
     * @param $request
     *
     * @return Sprint
     */
    public function put(Sprint $entity, $request)
    {
        return $this->processForm($entity, $request);
    }
    
    /**
     * @param Sprint $sprint
     * @param $request
     *
     * @return Sprint
     */
    public function patch(Sprint $entity, $request)
    {
        return $this->processForm($entity, $request, 'PATCH');
    }
    
    /**
     * @param Sprint $sprint
     *
     * @return Sprint
     */
    public function delete(Sprint $entity)
    {
        $time = new \DateTime('now');
        $entity->setDeleted($time);
        foreach ($entity->getTasks() as $task) {
            $task->setDeleted($time);
            $this->em->persist($task);
            $this->em->flush($task);
        }
        $this->em->persist($entity);
        $this->em->flush($entity);
    }
    
    /**
     * Processes the form.
     *
     * @param Sprint     $sprint
     * @param array         $parameters
     * @param String        $method
     *
     * @return Sprint
     *
     * @throws \Exception
     */
    private function processForm(Sprint $entity, $request, $method = "PUT")
    {
        $form = $this->factory->create(new SprintType(), $entity, array('method' => $method));
        $form->handleRequest($request);
        if (!$form->getErrors()) {
            $sprint = $form->getData();
            $this->em->persist($sprint);
            $this->em->flush($sprint);

            return $sprint;
        }

        throw new \Exception('Invalid submitted data');
    }
}