<?php

namespace EasyScrumREST\ProjectBundle\Handler;

use EasyScrumREST\ProjectBundle\Entity\Issue;
use EasyScrumREST\ProjectBundle\Entity\Backlog;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use EasyScrumREST\ProjectBundle\Form\BacklogRestType;
use Symfony\Component\HttpFoundation\Request;

class BacklogHandler
{
    private $em;
    private $factory;
    
    public function __construct(EntityManager $em, FormFactoryInterface $formFactory)
    {
        $this->em = $em;
        $this->factory = $formFactory;
    }

    public function get($salt)
    {
        return $this->em->getRepository('ProjectBundle:Backlog')->findOneBySalt($salt);
    }

    /**
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($search, $limit = 20, $offset = 0, $orderby = null)
    {
        $project=$this->em->getRepository('ProjectBundle:Project')->findOneBy(array('salt' => $search['project']));
        if(!$project)
            return array();
        if(isset($search['state']))
            return $this->em->getRepository('ProjectBundle:Backlog')->findBy(array('project' => $project->getId(), 'state'=>$search['state']));

        return $this->em->getRepository('ProjectBundle:Backlog')->findBy(array('project' => $project->getId()));
    }

    /**
     * Create a new Backlog.
     *
     * @param $request
     *
     * @return Backlog
     */
    public function post($request, $project)
    {
        $backlog = new Backlog();
        $backlog->setProject($project);

        return $this->processForm($backlog, $request, 'POST');
    }
    
    /**
     * @param Backlog $backlog
     * @param $request
     *
     * @return Backlog
     */
    public function put(Backlog $entity, $request)
    {
        return $this->processForm($entity, $request);
    }
    
    /**
     * @param Backlog $backlog
     * @param $request
     *
     * @return Backlog
     */
    public function patch(Backlog $entity, $request)
    {
        return $this->processForm($entity, $request, 'PATCH');
    }
    
    /**
     * @param Backlog $backlog
     *
     * @return Backlog
     */
    public function delete(Backlog $entity)
    {
        $this->em->remove($entity);
        $this->em->flush($entity);
    }
    
    /**
     * Processes the form.
     *
     * @param Backlog     $backlog
     * @param array         $parameters
     * @param String        $method
     *
     * @return Backlog
     *
     * @throws \Exception
     */
    private function processForm(Backlog $entity, $request, $method = "PUT")
    {
        $form = $this->factory->create(new BacklogRestType(), $entity, array('method' => $method));
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
     * @param Backlog $entity
     *
     */
    public function setStoryPoints(Request $request, Backlog $entity)
    {
        if($request->isMethod("POST")) {
            if($request->request->get('points')) {
                $entity->setPoints($request->request->get('points'));

                $this->em->persist($entity);
                $this->em->flush($entity);

                return true;
            }
        }

        return false;
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