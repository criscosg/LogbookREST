<?php

namespace EasyScrumREST\UserBundle\Handler;

use Doctrine\ORM\EntityManager;
use EasyScrumREST\UserBundle\Entity\TeamGroup;
use EasyScrumREST\UserBundle\Form\TeamGroupType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\BrowserKit\Request;

class TeamGroupHandler
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
        return $this->em->getRepository('UserBundle:TeamGroup')->find($id);
    }

    /**
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 20, $offset = 0, $orderby = null)
    {
        return $this->em->getRepository('UserBundle:TeamGroup')->findBy(array(), $orderby, $limit, $offset);
    }

    /**
     * Create a new TeamGroup.
     *
     * @param $request
     *
     * @return TeamGroup
     */
    public function post($request, $company)
    {
        $teamGroup = new TeamGroup();
        $teamGroup->setCompany($company);

        return $this->processForm($teamGroup, $request,'POST');
    }
    
    /**
     * @param TeamGroup $teamGroup
     * @param $request
     *
     * @return TeamGroup
     */
    public function put(TeamGroup $teamGroup, $request)
    {
        return $this->processForm($teamGroup, $request);
    }
    
    /**
     * @param TeamGroup $teamGroup
     * @param $request
     *
     * @return TeamGroup
     */
    public function patch(TeamGroup $teamGroup, $request)
    {
        return $this->processForm($teamGroup, $request,'PATCH');
    }
    
    /**
     * @param TeamGroup $teamGroup
     *
     * @return TeamGroup
     */
    public function delete(TeamGroup $teamGroup)
    {
        $this->em->remove($teamGroup);
        $this->em->flush($teamGroup);
    }
    
    /**
     * Processes the form.
     *
     * @param TeamGroup     $teamGroup
     * @param array         $parameters
     * @param String        $method
     *
     * @return TeamGroup
     *
     * @throws \Exception
     */
    private function processForm(TeamGroup $teamGroup, $request, $method = "PUT")
    {
        $form = $this->factory->create(new TeamGroupType($teamGroup->getCompany()->getId()), $teamGroup, array('method' => $method));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $teamGroup = $form->getData();
            $this->em->persist($teamGroup);
            $this->em->flush($teamGroup);

            return $teamGroup;
        }

        throw new \Exception('Invalid submitted data');
    }
    
}