<?php

namespace EasyScrumREST\ProjectBundle\Handler;

use EasyScrumREST\ProjectBundle\Form\ProjectRestType;

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
        return $this->em->getRepository('ProjectBundle:Project')->findOneBySalt($id);
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
    public function post($request, $company)
    {
        $project = new Project();
        $project->setCompany($company);

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
        $form = $this->factory->create(new ProjectRestType($this->em), $entity, array('method' => $method));
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
    public function createProject(FormInterface $form, $request, $owner=null)
    {
        $form->handleRequest($request);
        if ($form->isValid()) {
            $project = $form->getData();
            if($owner)
                $project->setOwner($owner);
            $this->em->persist($project);
            $this->em->flush($project);

            return $project;
        }

        throw new \Exception('Invalid submitted data');
    }
    
}