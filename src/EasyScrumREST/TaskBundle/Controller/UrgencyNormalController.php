<?php

namespace EasyScrumREST\TaskBundle\Controller;

use EasyScrumREST\TaskBundle\Form\TaskHoursType;

use EasyScrumREST\TaskBundle\Entity\Urgency;

use EasyScrumREST\TaskBundle\Form\CreateUrgencyType;
use EasyScrumREST\TaskBundle\Form\UrgencyHoursType;
use EasyScrumREST\SprintBundle\Entity\Sprint;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use EasyScrumREST\UserBundle\Entity\ApiUser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use EasyScrumREST\UserBundle\Form\RegisterType;
use EasyScrumREST\FrontendBundle\Controller\EasyScrumController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class UrgencyNormalController extends EasyScrumController
{

    /**
     * @ParamConverter("sprint", class="SprintBundle:Sprint")
     */
    public function newUrgencyAction(Sprint $sprint)
    {
        $urgency=new Urgency();
        $urgency->setProject($sprint->getProject());
        $form = $this->createForm(new CreateUrgencyType(), $urgency);
        $request=$this->getRequest();
        if($request->getMethod()=='POST'){
            $newUrgency = $this->get('urgency.handler')->handleUrgency($urgency, $request, 'POST');

            return $this->redirect($this->generateUrl('show_normal_sprint', array('id'=>$sprint->getId())));
        }

        return $this->render('TaskBundle:Urgency:create.html.twig', array('form' => $form->createView(), 'sprint'=>$sprint->getId()));
    }

    /**
     * @ParamConverter("urgency", class="TaskBundle:Urgency")
     * @ParamConverter("sprint", class="SprintBundle:Sprint")
     * 
     */
    public function editUrgencyAction(Urgency $urgency, Sprint $sprint)
    {
        $form = $this->createForm(new CreateUrgencyType(), $urgency);
        $request=$this->getRequest();
        if($request->getMethod()=='POST'){
            $urgency = $this->container->get('urgency.handler')->handleUrgency($urgency, $request);
            if($urgency) {
                return $this->redirect($this->generateUrl('show_normal_sprint', array('id'=>$sprint->getId())));
            }
        }

        return $this->render('TaskBundle:Urgency:create.html.twig', array('form' => $form->createView(),'edition' => true, 'urgency' => $urgency));
    }

    /**
     * @ParamConverter("urgency", class="TaskBundle:Urgency")
     */
    public function deleteUrgencyAction(Urgency $urgency)
    {
        $this->container->get('urgency.handler')->delete($urgency);
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $jsonResponse = json_encode(array('ok' => true));

            return $this->getHttpJsonResponse($jsonResponse);
        }

        return $this->redirect($this->generateUrl('sprints_list', array()));
    }

    /**
     * @Template("TaskBundle:Urgency:hours.html.twig")
     *
     * @ParamConverter("urgency", class="TaskBundle:Urgency")
     *
     * @return array
     */
    public function hoursFormAction(Urgency $urgency)
    {
        $form = $this->createForm(new TaskHoursType());

        return array('urgency'=>$urgency, 'form'=>$form->createView());
    }

    /**
     * @param Urgency $urgency
     *
     * @ParamConverter("urgency", class="TaskBundle:Urgency")
     */
    public function saveHoursAction(Urgency $urgency)
    {
        $request=$this->getRequest();
        if($request->getMethod()=='POST'){
            $text = $this->container->get('urgency.handler')->handleHoursUrgency($urgency, $this->getUser(), $request);
        }

        $jsonResponse = json_encode(array('text' => $text, 'urgency'=>$urgency->getId()));

        return $this->getHttpJsonResponse($jsonResponse);
    }

    /**
     * @Template("TaskBundle:Urgency:urgencies.html.twig")
     *
     * @ParamConverter("urgency", class="TaskBundle:Urgency")
     * @ParamConverter("sprint", class="SprintBundle:Sprint", options={"id" = "sprint"})
     *
     * @return array
     */
    public function moveToOnProcessAction(Urgency $urgency, Sprint $sprint)
    {
        $this->container->get('urgency.handler')->moveTo($urgency, 'ONPROCESS');
        $urgencies = $this->getDoctrine()->getRepository('TaskBundle:Urgency')->findBy(array('project'=>$sprint->getProject()->getId(),'sprint'=>null));
        $doneUrgencies = $this->getDoctrine()->getRepository('TaskBundle:Urgency')->findBy(array('sprint'=>$sprint->getId()));

        return array('urgencies'=> $urgencies, 'sprint'=>$sprint, 'doneUrgencies'=>$doneUrgencies);
    }

    /**
     * @Template("TaskBundle:Urgency:urgencies.html.twig")
     *
     * @ParamConverter("urgency", class="TaskBundle:Urgency")
     * @ParamConverter("sprint", class="SprintBundle:Sprint", options={"id" = "sprint"})
     *
     * @return array
     */
    public function moveToTodoAction(Urgency $urgency, Sprint $sprint)
    {
        $this->container->get('urgency.handler')->moveTo($urgency, 'TODO');
        $urgencies = $this->getDoctrine()->getRepository('TaskBundle:Urgency')->findBy(array('project'=>$sprint->getProject()->getId(),'sprint'=>null));
        $doneUrgencies = $this->getDoctrine()->getRepository('TaskBundle:Urgency')->findBy(array('sprint'=>$sprint->getId()));

        return array('urgencies'=> $urgencies, 'sprint'=>$sprint, 'doneUrgencies'=>$doneUrgencies);
    }

    /**
     * @Template("TaskBundle:Urgency:urgencies.html.twig")
     *
     * @ParamConverter("urgency", class="TaskBundle:Urgency")
     * @ParamConverter("sprint", class="SprintBundle:Sprint", options={"id" = "sprint"})
     *
     * @return array
     */
    public function moveToDoneAction(Urgency $urgency, Sprint $sprint)
    {
        $this->container->get('urgency.handler')->moveTo($urgency, 'DONE', $sprint);
        $urgencies = $this->getDoctrine()->getRepository('TaskBundle:Urgency')->findBy(array('project'=>$sprint->getProject()->getId(),'sprint'=>null));
        $doneUrgencies = $this->getDoctrine()->getRepository('TaskBundle:Urgency')->findBy(array('sprint'=>$sprint->getId()));

        return array('urgencies'=> $urgencies, 'sprint'=>$sprint, 'doneUrgencies'=>$doneUrgencies);
    }

    /**
     * @Template("TaskBundle:Urgency:urgencies.html.twig")
     *
     * @ParamConverter("urgency", class="TaskBundle:Urgency")
     *
     * @return array
     */
    public function moveToUndoneAction(Urgency $urgency)
    {
        $this->container->get('urgency.handler')->moveTo($urgency, 'UNDONE');
        $urgencies = $this->getDoctrine()->getRepository('TaskBundle:Urgency')->findAll();

        return array('urgencies'=> $urgencies);
    }

}