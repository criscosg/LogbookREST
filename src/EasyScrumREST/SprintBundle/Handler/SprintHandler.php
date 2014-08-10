<?php

namespace EasyScrumREST\SprintBundle\Handler;
use EasyScrumREST\SprintBundle\Form\SprintLastStepType;

use EasyScrumREST\UserBundle\Entity\Company;

use EasyScrumREST\SprintBundle\Form\SprintCreationFirstType;

use EasyScrumREST\SprintBundle\Entity\Sprint;
use EasyScrumREST\SprintBundle\Form\SprintType;
use EasyScrumREST\UserBundle\Handler\UserHandlerInterface;
use Doctrine\ORM\EntityManager;
use EasyScrumREST\UserBundle\Entity\AdminUser;
use Symfony\Component\Form\FormFactoryInterface;
use EasyScrumREST\UserBundle\Form\AdminUserType;
use Symfony\Component\BrowserKit\Request;

class SprintHandler
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
        return $this->em->getRepository('SprintBundle:Sprint')->find($id);
    }
    
    public function getActiveSprints($company)
    {
        return $this->em->getRepository('SprintBundle:Sprint')->findActiveSprints($company);
    }

    /**
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 20, $offset = 0, $orderby = null, $search=null)
    {

        return $this->em->getRepository('SprintBundle:Sprint')->findSprintBySearch($limit, $offset, $search, $orderby);
    }

    /**
     * Create a new Sprint.
     *
     * @param $request
     *
     * @return Sprint
     */
    public function post($request)
    {
        $sprint = new Sprint();

        return $this->processForm($sprint, $request, 'POST');
    }
    
    /**
     * @param Sprint $sprint
     * @param $request
     *
     * @return Sprint
     */
    public function put(Sprint $entity, $request)
    {
        return $this->processForm($entity, $request);
    }
    
    /**
     * @param Sprint $sprint
     * @param $request
     *
     * @return Sprint
     */
    public function patch(Sprint $entity, $request)
    {
        return $this->processForm($entity, $request, 'PATCH');
    }
    
    /**
     * @param Sprint $sprint
     *
     * @return Sprint
     */
    public function delete(Sprint $entity)
    {
        $time = new \DateTime('now');
        $entity->setDeleted($time);
        foreach ($entity->getTasks() as $task) {
            $task->setDeleted($time);
            $this->em->persist($task);
            $this->em->flush($task);
        }
        $this->em->persist($entity);
        $this->em->flush($entity);
    }
    
    /**
     * Processes the form.
     *
     * @param Sprint     $sprint
     * @param array         $parameters
     * @param String        $method
     *
     * @return Sprint
     *
     * @throws \Exception
     */
    private function processForm(Sprint $entity, $request, $method = "PUT")
    {
        $form = $this->factory->create(new SprintType(), $entity, array('method' => $method));
        $form->handleRequest($request);
        if (!$form->getErrors()) {
            $sprint = $form->getData();
            $this->em->persist($sprint);
            $this->em->flush($sprint);

            return $sprint;
        }

        throw new \Exception('Invalid submitted data');
    }
    
    public function firstStep($request,Company $company)
    {
        $sprint = new Sprint();
        $sprint->setCompany($company);
        $type=new SprintCreationFirstType();
        $type->setCompany($company->getId());
        $form = $this->factory->create($type, $sprint, array('method' => 'POST'));
        $form->handleRequest($request);
        if (!$form->getErrors()) {
            $sprint = $form->getData();
            $sprint->setHoursPlanified(($sprint->getHoursAvailable() * $sprint->getFocus())/100);
            $this->em->persist($sprint);
            $this->em->flush($sprint);
        
            return $sprint;
        }
        
        throw new \Exception($form->getErrorsAsString());
    }
    
    public function endPlanificationSprint($request, Sprint $sprint)
    {
        $form = $this->factory->create(new SprintLastStepType(), $sprint, array('method' => 'POST'));
        $form->handleRequest($request);
        if (!$form->getErrors()) {
            $sprint = $form->getData();
            $sprint->setPlanified(true);
            $sprint->setHoursPlanified($sprint->getPlanificationHours());
            $sprint->setFocus(($sprint->getHoursPlanified() * 100) / $sprint->getHoursAvailable());
            $this->em->persist($sprint);
            $this->em->flush($sprint);

            return $sprint;
        }

        throw new \Exception($form->getErrorsAsString());
    }
    
    public function finalizeSprint(Sprint $sprint)
    {
        foreach ($sprint->getTaskUndone() as $task) {
            if ($task->getState() != "UNDONE") {
                $task->setState("UNDONE");
                $this->em->persist($task);
            }
        }
        $sprint->setFinalized(true);
        $this->em->persist($sprint);
        $this->em->flush();
    }
}