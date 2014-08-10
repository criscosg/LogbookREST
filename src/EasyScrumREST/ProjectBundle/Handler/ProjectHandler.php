<?php

namespace EasyScrumREST\ProjectBundle\Handler;

use EasyScrumREST\ProjectBundle\Entity\Issue;

use EasyScrumREST\ProjectBundle\Entity\Backlog;

use Symfony\Component\Form\FormInterface;

use EasyScrumREST\ProjectBundle\Form\ProjectType;

use EasyScrumREST\ProjectBundle\Entity\Project;

use EasyScrumREST\UserBundle\Entity\Company;
use EasyScrumREST\UserBundle\Handler\UserHandlerInterface;
use Doctrine\ORM\EntityManager;
use EasyScrumREST\UserBundle\Entity\AdminUser;
use Symfony\Component\Form\FormFactoryInterface;
use EasyScrumREST\UserBundle\Form\AdminUserType;
use Symfony\Component\BrowserKit\Request;

class ProjectHandler
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
        return $this->em->getRepository('ProjectBundle:Project')->find($id);
    }

    /**
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($company, $limit = 20, $offset = 0, $orderby = null)
    {

        return $this->em->getRepository('ProjectBundle:Project')->findByCompany($company);
    }

    /**
     * Create a new Project.
     *
     * @param $request
     *
     * @return Project
     */
    public function post($request)
    {
        $project = new Project();

        return $this->processForm($project, $request, 'POST');
    }
    
    /**
     * @param Project $project
     * @param $request
     *
     * @return Project
     */
    public function put(Project $entity, $request)
    {
        return $this->processForm($entity, $request);
    }
    
    /**
     * @param Project $project
     * @param $request
     *
     * @return Project
     */
    public function patch(Project $entity, $request)
    {
        return $this->processForm($entity, $request, 'PATCH');
    }
    
    /**
     * @param Project $project
     *
     * @return Project
     */
    public function delete(Project $entity)
    {
        $this->em->remove($entity);
        $this->em->flush($entity);
    }
    
    /**
     * Processes the form.
     *
     * @param Project     $project
     * @param array         $parameters
     * @param String        $method
     *
     * @return Project
     *
     * @throws \Exception
     */
    private function processForm(Project $entity, $request, $method = "PUT")
    {
        $form = $this->factory->create(new ProjectType(), $entity, array('method' => $method));
        $form->handleRequest($request);
        if (!$form->getErrors()) {
            $project = $form->getData();
            $this->em->persist($project);
            $this->em->flush($project);

            return $project;
        }

        throw new \Exception('Invalid submitted data');
    }
    
    /**
     * Creates project.
     *
     * @param Project     $project
     * @param array         $parameters
     * @param String        $method
     *
     * @return Project
     *
     * @throws \Exception
     */
    public function createProject(FormInterface $form, $request)
    {
        $form->handleRequest($request);
        if ($form->isValid()) {
            $project = $form->getData();
            $this->em->persist($project);
            $this->em->flush($project);

            return $project;
        }

        throw new \Exception('Invalid submitted data');
    }
    
    /**
     * Creates Backlog.
     *
     * @param FormInterface $form
     * @param Request        $request
     *
     * @return Backlog
     *
     * @throws \Exception
     */
    public function createBacklog(FormInterface $form, $request)
    {
        $form->handleRequest($request);
        if ($form->isValid()) {
            $backlog = $form->getData();
            $this->em->persist($backlog);
            $this->em->flush($backlog);
    
            return $backlog;
        }
    
        throw new \Exception('Invalid submitted data');
    }
    
    /**
     * @param Backlog $backlog
     *
     */
    public function deleteBacklog(Backlog $entity)
    {
        $this->em->remove($entity);
        $this->em->flush($entity);
    }
    
    /**
     * @param Backlog $backlog
     *
     */
    public function finalizeBacklogTask(Backlog $entity)
    {
        $entity->setState('DONE');
        $this->em->persist($entity);
        $this->em->flush($entity);
    }

    /**
     * Creates Issue.
     *
     * @param FormInterface $form
     * @param Request        $request
     *
     * @return Issue
     *
     * @throws \Exception
     */
    public function createIssue(FormInterface $form, $request)
    {
        $form->handleRequest($request);
        if ($form->isValid()) {
            $issue = $form->getData();
            $this->em->persist($issue);
            $this->em->flush($issue);
    
            return $issue;
        }
    
        throw new \Exception('Invalid submitted data');
    }
    
    /**
     * @param Issue $entity
     *
     */
    public function finalizeIssue(Issue $entity)
    {
        $entity->setCompleted(true);
        $this->em->persist($entity);
        $this->em->flush($entity);
    }
    
}