<?php

namespace EasyScrumREST\TaskBundle\Handler;
use Doctrine\ORM\EntityManager;
use EasyScrumREST\TaskBundle\Entity\Task;
use Symfony\Component\Form\FormFactoryInterface;
use EasyScrumREST\TaskBundle\Form\TaskType;
use Symfony\Component\BrowserKit\Request;

class TaskHandler
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
        return $this->em->getRepository('EasyScrumREST:Task')->find($id);
    }

    /**
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 20, $offset = 0, $orderby = null)
    {
        return $this->em->getRepository('EasyScrumREST:Task')->findBy(array(), $orderby, $limit, $offset);
    }

    /**
     * Create a new Task.
     *
     * @param $request
     *
     * @return Task
     */
    public function post($request)
    {
        $task = new Task();

        return $this->processForm($task, $request, 'POST');
    }
    
    /**
     * @param Task $task
     * @param $request
     *
     * @return Task
     */
    public function put(Task $task, $request)
    {
        return $this->processForm($task, $request);
    }
    
    /**
     * @param Task $task
     * @param $request
     *
     * @return Task
     */
    public function patch(Task $task, $request)
    {
        return $this->processForm($task, $request, 'PATCH');
    }
    
    /**
     * @param Task $task
     *
     * @return Task
     */
    public function delete(Task $task)
    {
        $this->em->remove($task);
        $this->em->flush($task);
    }
    
    /**
     * Processes the form.
     *
     * @param Task     $task
     * @param array         $parameters
     * @param String        $method
     *
     * @return Task
     *
     * @throws \Exception
     */
    private function processForm(Task $task, $request, $method = "PUT")
    {
        $form = $this->factory->create(new TaskType(), $task, array('method' => $method));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $task = $form->getData();
            $this->em->persist($task);
            $this->em->flush($task);

            return $task;
        }

        throw new \Exception('Invalid submitted data');
    }
}