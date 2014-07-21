<?php
namespace EasyScrumREST\ProjectBundle\Controller;

use EasyScrumREST\ProjectBundle\Entity\Project;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use EasyScrumREST\UserBundle\Form\RegisterType;
use EasyScrumREST\FrontendBundle\Controller\EasyScrumController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class ProjectController extends EasyScrumController
{
    public function listProjectsAction()
    {
        $user = $this->getUser();
        $search['company'] = $user->getCompany()->getId();
        $projects=$this->container->get('project.handler')->all(20, 0, null, $search);

        return $this->render('ProjectBundle:Project:index.html.twig', array('projects'=>$projects));
    }

    /**
     * @ParamConverter("project", class="ProjectBundle:Project")
     */
    public function projectShowAction(Project $project)
    {
        $urgencies = $this->getDoctrine()->getRepository('TaskBundle:Urgency')->findBy(array('project'=>null));
        $doneUrgencies = $this->getDoctrine()->getRepository('TaskBundle:Urgency')->findBy(array('project'=>$project->getId()));

        return $this->render('ProjectBundle:Project:view.html.twig', array('project' => $project, 'urgencies'=>$urgencies, 'doneUrgencies'=>$doneUrgencies));
    }

    public function newProjectAction()
    {
        $form = $this->createForm(new ProjectCreationFirstType());
        $request=$this->getRequest();
        if($request->getMethod()=='POST'){
            $newProject = $this->get('project.handler')->firstStep($request, $this->getUser()->getCompany());
            if($newProject) {

                return $this->redirect($this->generateUrl('project_planification', array('id'=>$newProject->getId())));
            }
        }

        return $this->render('ProjectBundle:Project:first-step.html.twig', array('form' => $form->createView()));
    }
    
    /**
     * @ParamConverter("project", class="ProjectBundle:Project")
     */
    public function deleteProjectAction(Project $project)
    {
        $this->get('project.handler')->delete($project);
    
        return $this->redirect($this->generateUrl('projects_list'));
    }
    
}