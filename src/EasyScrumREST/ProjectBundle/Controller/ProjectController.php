<?php
namespace EasyScrumREST\ProjectBundle\Controller;

use EasyScrumREST\ActionBundle\Entity\ActionBacklog;

use EasyScrumREST\ActionBundle\Entity\ActionProject;

use EasyScrumREST\ProjectBundle\Form\IssueType;

use EasyScrumREST\ProjectBundle\Entity\Issue;

use EasyScrumREST\ProjectBundle\Form\BacklogType;
use EasyScrumREST\ProjectBundle\Entity\Backlog;
use EasyScrumREST\ProjectBundle\Form\ProjectWithOwnerType;
use EasyScrumREST\ProjectBundle\Form\ProjectType;
use EasyScrumREST\ProjectBundle\Entity\Project;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use EasyScrumREST\UserBundle\Form\RegisterType;
use EasyScrumREST\FrontendBundle\Controller\EasyScrumController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ProjectController extends EasyScrumController
{
    public function listProjectsAction()
    {
        $user = $this->getUser();
        $projects=$this->container->get('project.handler')->all($user->getCompany()->getId(),20, 0, null);

        return $this->render('ProjectBundle:Project:index.html.twig', array('projects'=>$projects));
    }

    /**
     * @ParamConverter("project", class="ProjectBundle:Project")
     */
    public function projectShowAction(Project $project)
    {
        return $this->render('ProjectBundle:Project:view.html.twig', array('project' => $project));
    }

    public function newProjectAction()
    {
        $project = new Project();
        $project->setCompany($this->getUser()->getCompany());
        if($this->container->get('security.context')->isGranted('ROLE_SCRUM_MASTER')){
            $type = new ProjectWithOwnerType();
            $type->setCompany($this->getUser()->getCompany());
            $form = $this->createForm($type, $project);
        } elseif($this->container->get('security.context')->isGranted('ROLE_PRODUCT_OWNER')) {
            $form = $this->createForm(new ProjectType(), $project);
        } else {
            return $this->redirect($this->generateUrl('projects_list'));
        }
        $request=$this->getRequest();
        if ($request->getMethod()=='POST') {
            if($this->container->get('security.context')->isGranted('ROLE_PRODUCT_OWNER')) {
                $newProject = $this->get('project.handler')->createProject($form, $request, $this->getUser());
            } else {
                $newProject = $this->get('project.handler')->createProject($form, $request);
            }
            if($newProject) {
                $this->get('action.manager')->createProjectAction($newProject, $this->getUser(), ActionProject::CREATE_PROJECT);

                return $this->redirect($this->generateUrl('projects_list'));
            }
        }

        return $this->render('ProjectBundle:Project:create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @ParamConverter("project", class="ProjectBundle:Project")
     */
    public function editProjectAction(Project $project)
    {
        if($this->container->get('security.context')->isGranted('ROLE_SCRUM_MASTER')){
            $type = new ProjectWithOwnerType();
            $type->setCompany($this->getUser()->getCompany());
            $form = $this->createForm($type, $project);
        } elseif($this->container->get('security.context')->isGranted('ROLE_PRODUCT_OWNER')) {
            $form = $this->createForm(new ProjectType(), $project);
        } else {
            return $this->redirect($this->generateUrl('projects_list'));
        }
        $request=$this->getRequest();
        if($request->getMethod()=='POST'){
            $newProject = $this->get('project.handler')->createProject($form, $request);
            if($newProject) {
                $this->get('action.manager')->createProjectAction($newProject, $this->getUser(), ActionProject::EDIT_PROJECT);
                
                return $this->redirect($this->generateUrl('projects_list'));
            }
        }

        return $this->render('ProjectBundle:Project:create.html.twig', array('form' => $form->createView(), 'id'=>$project->getId(), 'edition'=>true));
    }

    /**
     * @ParamConverter("project", class="ProjectBundle:Project")
     */
    public function deleteProjectAction(Project $project)
    {
        $this->get('project.handler')->delete($project);

        return $this->redirect($this->generateUrl('projects_list'));
    }

    /**
     * @ParamConverter("project", class="ProjectBundle:Project")
     */
    public function newBacklogAction(Project $project)
    {
        $backlog = new Backlog();
        $backlog->setProject($project);
        $form = $this->createForm(new BacklogType(), $backlog);
        $request=$this->getRequest();
        if ($request->getMethod()=='POST') {
            $newBacklog = $this->get('backlog.handler')->createBacklog($form, $request);
            if($newBacklog) {
                $this->get('action.manager')->createBacklogAction($newBacklog, $this->getUser(), ActionBacklog::CREATE_BACKLOG);
    
                return $this->redirect($this->generateUrl('show_normal_project', array('id'=>$project->getId())));
            }
        }
    
        return $this->render('ProjectBundle:Backlog:create.html.twig', array('form' => $form->createView(), 'project'=>$project));
    }

    /**
     * @ParamConverter("backlog", class="ProjectBundle:Backlog")
     */
    public function editBacklogAction(Backlog $backlog)
    {
        $form = $this->createForm(new BacklogType(), $backlog);
        $request=$this->getRequest();
        if($request->getMethod()=='POST'){
            $newProject = $this->get('backlog.handler')->createBacklog($form, $request);
            if($newProject) {
                $this->get('action.manager')->createBacklogAction($newBacklog, $this->getUser(), ActionBacklog::EDIT_BACKLOG);
    
                return $this->redirect($this->generateUrl('show_normal_project', array('id'=>$backlog->getProject()->getId())));
            }
        }

        return $this->render('ProjectBundle:Backlog:create.html.twig', array('form' => $form->createView(), 'project'=>$backlog->getProject(), 'backlog'=>$backlog, 'edition'=>true));
    }
    
    /**
     * @ParamConverter("backlog", class="ProjectBundle:Backlog")
     */
    public function deleteBacklogAction(Backlog $backlog)
    {
        $this->get('backlog.handler')->deleteBacklog($backlog);
    
        return $this->redirect($this->generateUrl('show_normal_project', array('id'=>$backlog->getProject()->getId())));
    }

    /**
     * @Template("ProjectBundle:Backlog:backlog_table.html.twig")
     *
     * @ParamConverter("backlog", class="ProjectBundle:Backlog")
     *
     * @return array
     */
    public function finalizeBacklogAction(Backlog $backlog)
    {
        $this->container->get('backlog.handler')->finalizeBacklogTask($backlog);
        $this->get('action.manager')->createBacklogAction($backlog, $this->getUser(), ActionBacklog::FINALIZED_BACKLOG);
    
        return array('task'=> $backlog);
    }

    /**
     * @ParamConverter("backlog", class="ProjectBundle:Backlog")
     */
    public function newIssueAction(Backlog $backlog)
    {
        $issue = new Issue();
        $issue->setBacklog($backlog);
        $form = $this->createForm(new IssueType(), $issue);
        $request=$this->getRequest();
        if ($request->getMethod()=='POST') {
            $newBacklog = $this->get('backlog.handler')->createIssue($form, $request);
            if($newBacklog) {
                $this->get('action.manager')->createBacklogAction($backlog, $this->getUser(), ActionBacklog::NEW_ISSUE);
    
                return $this->redirect($this->generateUrl('show_normal_project', array('id'=>$issue->getBacklog()->getProject()->getId())));
            }
        }
    
        return $this->render('ProjectBundle:Issue:create.html.twig', array('form' => $form->createView(), 'backlog'=>$backlog));
    }

    /**
     * @ParamConverter("issue", class="ProjectBundle:Issue")
     */
    public function editIssueAction(Issue $issue)
    {
        $form = $this->createForm(new IssueType(), $issue);
        $request=$this->getRequest();
        if($request->getMethod()=='POST'){
            $newProject = $this->get('backlog.handler')->createIssue($form, $request);
            if($newProject) {
    
                return $this->redirect($this->generateUrl('show_normal_project', array('id'=>$issue->getBacklog()->getProject()->getId())));
            }
        }

        return $this->render('ProjectBundle:Issue:create.html.twig', array('form' => $form->createView(), 'backlog'=>$issue->getBacklog(), 'issue'=>$issue, 'edition'=>true));
    }

    /**
     * @Template("ProjectBundle:Backlog:backlog_table.html.twig")
     * 
     * @ParamConverter("issue", class="ProjectBundle:Issue")
     */
    public function finalizeIssueAction(Issue $issue)
    {
        $this->container->get('backlog.handler')->finalizeIssue($issue);
        
        return array('task'=> $issue, 'issue'=>true);
    }
}