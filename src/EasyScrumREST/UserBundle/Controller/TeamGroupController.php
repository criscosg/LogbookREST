<?php

namespace EasyScrumREST\UserBundle\Controller;

use EasyScrumREST\FrontendBundle\Controller\EasyScrumController;
use EasyScrumREST\UserBundle\Form\TeamGroupType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class TeamGroupController extends EasyScrumController
{
    public function listAction()
    {
        $teamGroups = $users=$this->getUser()->getCompany()->getTeamGroups();

        return $this->render('UserBundle:TeamGroup:index.html.twig', array('groups' => $teamGroups));
    }

    /**
     * @ParamConverter("teamGroup", class="UserBundle:TeamGroup")
     */
    public function showAction($teamGroup)
    {
        return $this->render('UserBundle:TeamGroup:view.html.twig', array('group' => $teamGroup));
    }

    public function newAction(Request $request)
    {
        $company = $this->getUser()->getCompany();
        $form = $this->createForm(new TeamGroupType($company->getId()));
        if($request->getMethod()=='POST'){
            $newTeamGroup = $this->get('team_group.handler')->post($request, $company);
            if($newTeamGroup) {
                return $this->redirect($this->generateUrl('team_group_list'));
            }
        }

        return $this->render('UserBundle:TeamGroup:create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @ParamConverter("teamGroup", class="UserBundle:TeamGroup")
     */
    public function editAction(Request $request, $teamGroup)
    {
        $form = $form = $this->createForm(new TeamGroupType($this->getUser()->getCompany()->getId()), $teamGroup);
        if($request->getMethod()=='POST'){
            $this->container->get('team_group.handler')->put($teamGroup, $request);
            if($teamGroup) {
                return $this->redirect($this->generateUrl('team_group_list'));
            }
        }

        return $this->render('UserBundle:TeamGroup:create.html.twig', array('form' => $form->createView(),'edition' => true, 'group' => $teamGroup));
    }

    /**
     * @ParamConverter("teamGroup", class="UserBundle:TeamGroup")
     */
    public function deleteTeamGroupAction($teamGroup)
    {
        $this->container->get('team_group.handler')->delete($teamGroup);

        return $this->redirect($this->generateUrl('team_group_list'));
    }
}