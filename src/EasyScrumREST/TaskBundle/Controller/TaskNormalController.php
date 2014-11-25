<?php
namespace EasyScrumREST\TaskBundle\Controller;

use EasyScrumREST\TaskBundle\Form\SearchTaskType;
use EasyScrumREST\TaskBundle\Form\TaskHoursType;
use EasyScrumREST\SprintBundle\Entity\Sprint;
use EasyScrumREST\TaskBundle\Form\CreateTaskType;
use EasyScrumREST\TaskBundle\Entity\Task;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use EasyScrumREST\UserBundle\Entity\ApiUser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use EasyScrumREST\UserBundle\Form\RegisterType;
use EasyScrumREST\FrontendBundle\Controller\EasyScrumController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class TaskNormalController extends EasyScrumController
{
    /**
     * @ParamConverter("task", class="TaskBundle:Task")
     */
    public function taskShowAction(Task $task)
    {
        return $this->render('TaskBundle:Task:view.html.twig', array('task' => $task));
    }

    /**
     * @ParamConverter("sprint", class="SprintBundle:Sprint")
     */
    public function newTaskAction(Sprint $sprint)
    {
        $task=new Task();
        $task->setSprint($sprint);
        $form = $this->createForm(new CreateTaskType(), $task);
        $request=$this->getRequest();
        if($request->getMethod()=='POST'){
            $newTask = $this->get('task.handler')->handleTask($task, $request, 'POST');
            if(!$newTask->getSprint()->getPlanified()) {
                return $this->redirect($this->generateUrl('sprint_planification', array('id'=>$sprint->getId())));
            } else {
                return $this->redirect($this->generateUrl('show_normal_sprint', array('id'=>$sprint->getId())));
            }
        }

        return $this->render('TaskBundle:Task:create.html.twig', array('form' => $form->createView(), 'sprint'=>$sprint->getId()));
    }

    /**
     * @ParamConverter("task", class="TaskBundle:Task")
     */
    public function editTaskAction(Task $task)
    {
        $form = $this->createForm(new CreateTaskType(), $task);
        $request=$this->getRequest();
        if($request->getMethod()=='POST'){
            $task = $this->container->get('task.handler')->handleTask($task, $request);
            if($task) {
                return $this->redirect($this->generateUrl('sprint_planification', array('id'=>$task->getSprint()->getId())));
            }
        }

        return $this->render('TaskBundle:Task:create.html.twig', array('form' => $form->createView(),'edition' => true, 'task' => $task));
    }

    /**
     * @ParamConverter("task", class="TaskBundle:Task")
     */
    public function deleteTaskAction(Task $task)
    {
        $this->container->get('task.handler')->delete($task);
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $planifiedHours = $task->getSprint()->getPlanificationHours();
            $jsonResponse = json_encode(array('ok' => true, 'planified'=>$planifiedHours));
            
            return $this->getHttpJsonResponse($jsonResponse);
        }

        return $this->redirect($this->generateUrl('sprint_planification', array('id'=>$task->getSprint()->getId())));
    }
    
    /**
     * @Template("TaskBundle:Task:hours.html.twig")
     * 
     * @ParamConverter("task", class="TaskBundle:Task")
     *
     * @return array
     */
    public function hoursFormAction(Task $task)
    {
        $message=null;
        $form = $this->createForm(new TaskHoursType());
        if (($task->getUser() && !$task->getUser()->isEqualTo($this->getUser()))) {
            $message = "Task blocked by another user";
        }
        
        return array('task'=>$task, 'form'=>$form->createView(), 'message'=>$message);
    }
    
    /**
     * @param Task $task
     * 
     * @ParamConverter("task", class="TaskBundle:Task")
     */
    public function saveHoursAction(Task $task)
    {
        $request=$this->getRequest();
        if($request->getMethod()=='POST'){
            $text = $this->container->get('task.handler')->handleHoursTask($task, $this->getUser(), $request);
        }

        $jsonResponse = json_encode(array('text' => $text, 'task'=>$task->getId()));

        return $this->getHttpJsonResponse($jsonResponse);
    }

    /**
     * @Template("TaskBundle:Task:sprint-tasks.html.twig")
     *
     * @ParamConverter("task", class="TaskBundle:Task")
     *
     * @return array
     */
    public function moveToOnProcessAction(Task $task)
    {
        if (($task->getUser() && $task->getUser()->isEqualTo($this->getUser())) || ! $task->getUser()) {
            $this->container->get('task.handler')->moveTo($task, 'ONPROCESS');
        }
        
        return array('sprint'=> $task->getSprint());
    }
    
    /**
     * @Template("TaskBundle:Task:sprint-tasks.html.twig")
     *
     * @ParamConverter("task", class="TaskBundle:Task")
     *
     * @return array
     */
    public function moveToTodoAction(Task $task)
    {
        if (($task->getUser() && $task->getUser()->isEqualTo($this->getUser())) || ! $task->getUser()) {
            $this->container->get('task.handler')->moveTo($task, 'TODO');
        }
    
        return array('sprint'=> $task->getSprint());
    }
    
    /**
     * @Template("TaskBundle:Task:sprint-tasks.html.twig")
     *
     * @ParamConverter("task", class="TaskBundle:Task")
     *
     * @return array
     */
    public function moveToDoneAction(Task $task)
    {
        if (($task->getUser() && $task->getUser()->isEqualTo($this->getUser())) || ! $task->getUser()) {
            $this->container->get('task.handler')->moveTo($task, 'DONE');
        }
    
        return array('sprint'=> $task->getSprint());
    }
    
    /**
     * @Template("TaskBundle:Task:sprint-tasks.html.twig")
     *
     * @ParamConverter("task", class="TaskBundle:Task")
     *
     * @return array
     */
    public function moveToUndoneAction(Task $task)
    {
        if (($task->getUser() && $task->getUser()->isEqualTo($this->getUser())) || ! $task->getUser()) {
            $this->container->get('task.handler')->moveTo($task, 'UNDONE');
        }

        return array('sprint'=> $task->getSprint());
    }
    
    /**
     * @ParamConverter("task", class="TaskBundle:Task")
     *
     * @return array
     */
    public function taskLockAction(Task $task)
    {
        if (($task->getUser() && $task->getUser()->isEqualTo($this->getUser())) || ! $task->getUser()) {
            $this->container->get('task.handler')->lockTask($task, $this->getUser());
            $jsonResponse = json_encode(array('ok' => true));
        } else {
            $jsonResponse = json_encode(array('ok' => false));
        }

        return $this->getHttpJsonResponse($jsonResponse);
    }
    
    /**
     * @ParamConverter("task", class="TaskBundle:Task")
     *
     * @return array
     */
    public function taskUnlockAction(Task $task)
    {
        if (($task->getUser() && $task->getUser()->isEqualTo($this->getUser())) || ! $task->getUser()) {
            $this->container->get('task.handler')->unlockTask($task);
            $jsonResponse = json_encode(array('ok' => true));
        } else {
            $jsonResponse = json_encode(array('ok' => false));
        }

        return $this->getHttpJsonResponse($jsonResponse);
    }

    public function facturationAction(Request $request)
    {
        $paginator = $this->get('ideup.simple_paginator');
        $form = $this->createForm(new SearchTaskType());
        $search = $this->get('task.handler')->search($form, $request);
        $tasks = $this->get('task.handler')->paginate($search, $paginator);
        $totals = $this->get('task.handler')->totals($search);
        $company = $this->getUser()->getCompany();

        return $this->render('TaskBundle:Task:facturation.html.twig', array('form' => $form->createView(), 'paginator'=>$paginator , 'tasks' => $tasks, 'company'=>$company, 'totals'=>$totals));
    }
}