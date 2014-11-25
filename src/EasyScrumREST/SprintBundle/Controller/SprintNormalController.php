<?php
namespace EasyScrumREST\SprintBundle\Controller;

use EasyScrumREST\SprintBundle\Entity\HoursSprint;
use EasyScrumREST\SprintBundle\Form\SprintHourType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
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
    public function listSprintsAction(Request $request)
    {
        $user = $this->getUser();
        $search['company'] = $user->getCompany()->getId();
        $project=$request->query->get('project');
        $search['project'] = $project;
        $sprints=$this->container->get('sprint.handler')->all(20, 0, null, $search);

        return $this->render('SprintBundle:Sprint:index.html.twig', array('sprints'=>$sprints));
    }

    /**
     * @ParamConverter("sprint", class="SprintBundle:Sprint")
     */
    public function sprintShowAction(Sprint $sprint)
    {
        $urgencies = $this->getDoctrine()->getRepository('TaskBundle:Urgency')->findBy(array('project'=>$sprint->getProject()->getId(),'sprint'=>null));
        $doneUrgencies = $this->getDoctrine()->getRepository('TaskBundle:Urgency')->findBy(array('sprint'=>$sprint->getId()));
        $today=new \DateTime('today');

        return $this->render('SprintBundle:Sprint:view.html.twig', array('sprint' => $sprint, 'urgencies'=>$urgencies, 'chartHours'=>$sprint->getChartHoursArray(),
                                            'doneUrgencies'=>$doneUrgencies, 'today'=>$today->format('d/m')));
    }

    public function newSprintFirstStepAction()
    {
        $type=new SprintCreationFirstType();
        $type->setCompany($this->getUser()->getCompany());
        $form = $this->createForm($type);
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
        $charts=$this->get('sprint.focus_member')->getChartFocusMember($sprint);
        $users=array();
        $generalChart=array();
        foreach ($charts as $key=>$value){
            $users[$key]=$this->getDoctrine()->getRepository('UserBundle:ApiUser')->findOneById($key);
            foreach ($value as $subkey=>$subvalue) {
                if(isset($generalChart[$subkey])){
                    $generalChart[$subkey] = $generalChart[$subkey] + ($subvalue/count($charts));
                } else {
                    $generalChart[$subkey] = $subvalue/count($charts);
                }
            }
        }

        return $this->render('SprintBundle:Sprint:finalized.html.twig', array('sprint' => $sprint, 'charts'=>$charts,
                                                                             'users'=> $users, 'generalChart'=>$generalChart));
    }

    /**
     * @ParamConverter("sprint", class="SprintBundle:Sprint")
     */
    public function deleteSprintAction(Sprint $sprint)
    {
        $this->get('sprint.handler')->delete($sprint);

        return $this->redirect($this->generateUrl('sprints_list'));
    }
    
    /**
     * @Template("SprintBundle:Sprint:hours.html.twig")
     * @ParamConverter("sprint", class="SprintBundle:Sprint")
     *
     * @return array
     */
    public function hoursSprintAction(Sprint $sprint)
    {
        $hours = new HoursSprint();
        $hours->setDate(new \DateTime('today'));
        $form = $this->createForm(new SprintHourType(), $hours);
    
        return array('sprint'=>$sprint, 'form'=>$form->createView());
    }
    
    /**
     * @ParamConverter("sprint", class="SprintBundle:Sprint")
     */
    public function saveHoursSprintAction(Request $request, Sprint $sprint)
    {
        $form = $this->createForm(new SprintHourType());
        if($request->getMethod()=='POST'){
            $hour = $this->get('sprint.handler')->saveHoursSprint($request, $sprint);
            if($hour) {
                return $this->redirect($this->generateUrl('show_normal_sprint', array('id'=>$sprint->getId())));
            }
        }

        return $this->redirect($this->generateUrl('show_normal_sprint', array('id'=>$sprint->getId())));
    }
}