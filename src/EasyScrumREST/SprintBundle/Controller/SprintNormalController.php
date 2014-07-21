<?php
namespace EasyScrumREST\SprintBundle\Controller;

use EasyScrumREST\SprintBundle\Form\SprintLastStepType;
use EasyScrumREST\SprintBundle\Form\SprintCreationFirstType;
use EasyScrumREST\SprintBundle\Entity\Sprint;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use EasyScrumREST\UserBundle\Form\RegisterType;
use EasyScrumREST\FrontendBundle\Controller\EasyScrumController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class SprintNormalController extends EasyScrumController
{
    public function listSprintsAction()
    {
        $user = $this->getUser();
        $search['company'] = $user->getCompany()->getId();
        $sprints=$this->container->get('sprint.handler')->all(20, 0, null, $search);

        return $this->render('SprintBundle:Sprint:index.html.twig', array('sprints'=>$sprints));
    }

    /**
     * @ParamConverter("sprint", class="SprintBundle:Sprint")
     */
    public function sprintShowAction(Sprint $sprint)
    {
        $urgencies = $this->getDoctrine()->getRepository('TaskBundle:Urgency')->findBy(array('sprint'=>null));
        $doneUrgencies = $this->getDoctrine()->getRepository('TaskBundle:Urgency')->findBy(array('sprint'=>$sprint->getId()));

        return $this->render('SprintBundle:Sprint:view.html.twig', array('sprint' => $sprint, 'urgencies'=>$urgencies, 'doneUrgencies'=>$doneUrgencies));
    }

    public function newSprintFirstStepAction()
    {
        $form = $this->createForm(new SprintCreationFirstType());
        $request=$this->getRequest();
        if($request->getMethod()=='POST'){
            $newSprint = $this->get('sprint.handler')->firstStep($request, $this->getUser()->getCompany());
            if($newSprint) {

                return $this->redirect($this->generateUrl('sprint_planification', array('id'=>$newSprint->getId())));
            }
        }

        return $this->render('SprintBundle:Sprint:first-step.html.twig', array('form' => $form->createView()));
    }
    
    /**
     * @ParamConverter("sprint", class="SprintBundle:Sprint")
     */
    public function sprintPlanificationAction(Sprint $sprint)
    {
        $form = $this->createForm(new SprintLastStepType(), $sprint);
        $request=$this->getRequest();
        if($request->getMethod()=='POST'){
            $sprint = $this->get('sprint.handler')->endPlanificationSprint($request, $sprint);
            if($sprint) {
                return $this->redirect($this->generateUrl('sprints_list'));
            }
        }

        return $this->render('SprintBundle:Sprint:planification.html.twig', array('sprint' => $sprint, 'form'=>$form->createView()));
    }
    
    /**
     * @ParamConverter("sprint", class="SprintBundle:Sprint")
     */
    public function sprintFinalizeAction(Sprint $sprint)
    {
        if(!$sprint->getFinalized()){
            $this->get('sprint.handler')->finalizeSprint($sprint);
        }
        $doneUrgencies = $this->getDoctrine()->getRepository('TaskBundle:Urgency')->findBy(array('sprint'=>$sprint->getId()));

        return $this->render('SprintBundle:Sprint:finalized.html.twig', array('sprint' => $sprint));
    }

    /**
     * @ParamConverter("sprint", class="SprintBundle:Sprint")
     */
    public function deleteSprintAction(Sprint $sprint)
    {
        $this->get('sprint.handler')->delete($sprint);

        return $this->redirect($this->generateUrl('sprints_list'));
    }
}