<?php

namespace EasyScrumREST\TaskBundle\Handler;
use EasyScrumREST\ActionBundle\Entity\ActionTask;

use EasyScrumREST\ActionBundle\Manager\ActionManager;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;
use EasyScrumREST\TaskBundle\Entity\HoursSpent;
use EasyScrumREST\TaskBundle\Form\TaskHoursType;
use EasyScrumREST\TaskBundle\Form\CreateTaskType;
use Doctrine\ORM\EntityManager;
use EasyScrumREST\TaskBundle\Entity\Task;
use Symfony\Component\Form\FormFactoryInterface;
use EasyScrumREST\TaskBundle\Form\TaskType;

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
        return $this->em->getRepository('TaskBundle:Task')->find($id);
    }

    public function getSalt($salt)
    {
        return $this->em->getRepository('TaskBundle:Task')->findOneBySalt($salt);
    }

    /**
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 20, $offset = 0, $orderby = null)
    {
        return $this->em->getRepository('TaskBundle:Task')->findBy(array(), $orderby, $limit, $offset);
    }
    
    public function search(FormInterface $form, Request $request, $company)
    {
        $criteria = array();
        $search=array();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $criteria = $form->getData();
            $criteria['company'] = $company->getId();
            $search = $this->em->getRepository('TaskBundle:Task')->findBySearch($criteria);
        }

        return $search;
    }
    
    public function paginate($search, $paginator)
    {
        $tasks = $paginator->setItemsPerPage(20)->paginate($search)->getResult();
    
        return $tasks;
    }
    
    public function totals($tasks)
    {
        $tasks = $tasks->getResult();
        $hours=0;
        foreach ($tasks as $task) {
            $hours += $task->getHoursSpent();
        }
    
        return array('count' => count($tasks), 'hours' => $hours);
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
    
    /**
     * Creates/Edit task
     *
     * @param Task     $task
     * @param array         $parameters
     * @param String        $method
     *
     * @return Task
     *
     * @throws \Exception
     */
    public function handleTask(Task $task, $request, $method = "POST")
    {
        $form = $this->factory->create(new CreateTaskType(), $task, array('method' => $method));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $task = $form->getData();
            $task->setSalt();
            $this->em->persist($task);
            $this->em->flush($task);
    
            return $task;
        }
    
        throw new \Exception($form->getErrorsAsString());
    }
    
    /**
     * Creates/Edit task
     *
     * @param Task     $task
     * @param array         $parameters
     * @param String        $method
     *
     * @return Task
     *
     * @throws \Exception
     */
    public function handleHoursTask(Task $task, $user, $request)
    {
        $hours = new HoursSpent();
        $hours->setTask($task);
        $hours->setUser($user);
        $form = $this->factory->create(new TaskHoursType(), $hours);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->em->persist($hours);
            $this->em->flush($hours);

            return $task->getHoursSpent()."/".$task->getHoursEnd();
        }
    
        throw new \Exception($form->getErrorsAsString());
    }
    
    /**
     * Set task state to $state.
     *
     * @param Task $task
     * @param String $state
     *
     * @return Task
     */
    public function moveTo(Task $task, $state)
    {
        $task->setState($state);
        $this->em->persist($task);
        $this->em->flush($task);
    }
    
    public function lockTask(Task $task, $user)
    {
        $task->setUser($user);
        $this->em->persist($task);
        $this->em->flush($task);
    }
    
    public function unlockTask(Task $task)
    {
        $task->setUser();
        $this->em->persist($task);
        $this->em->flush($task);
    }
}