<?php

namespace EasyScrumREST\TaskBundle\Handler;
use EasyScrumREST\TaskBundle\Form\UrgencyHoursType;
use EasyScrumREST\TaskBundle\Form\CreateUrgencyType;
use EasyScrumREST\TaskBundle\Form\UrgencyType;
use EasyScrumREST\TaskBundle\Entity\Urgency;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\BrowserKit\Request;

class UrgencyHandler
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
        return $this->em->getRepository('TaskBundle:Urgency')->find($id);
    }

    /**
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 20, $offset = 0, $orderby = null)
    {
        return $this->em->getRepository('TaskBundle:Urgency')->findBy(array(), $orderby, $limit, $offset);
    }

    /**
     * Create a new Task.
     *
     * @param $request
     *
     * @return Task
     */
    public function post($request)
    {
        $urgency = new Urgency();

        return $this->processForm($urgency, $request, 'POST');
    }
    
    /**
     * @param Urgency $urgency
     * @param $request
     *
     * @return Urgency
     */
    public function put(Urgency $urgency, $request)
    {
        return $this->processForm($urgency, $request);
    }
    
    /**
     * @param Urgency $urgency
     * @param $request
     *
     * @return Urgency
     */
    public function patch(Urgency $urgency, $request)
    {
        return $this->processForm($urgency, $request, 'PATCH');
    }
    
    /**
     * @param Urgency $urgency
     *
     * @return Urgency
     */
    public function delete(Urgency $urgency)
    {
        $this->em->remove($urgency);
        $this->em->flush($urgency);
    }
    
    /**
     * Processes the form.
     *
     * @param Urgency     $urgency
     * @param array         $parameters
     * @param String        $method
     *
     * @return Urgency
     *
     * @throws \Exception
     */
    private function processForm(Urgency $urgency, $request, $method = "PUT")
    {
        $form = $this->factory->create(new UrgencyType(), $urgency, array('method' => $method));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $urgency = $form->getData();
            $this->em->persist($urgency);
            $this->em->flush($urgency);

            return $urgency;
        }

        throw new \Exception('Invalid submitted data');
    }
    
    /**
     * Creates/Edit urgency
     *
     * @param Urgency     $urgency
     * @param array         $parameters
     * @param String        $method
     *
     * @return Urgency
     *
     * @throws \Exception
     */
    public function handleUrgency(Urgency $urgency, $request, $method = "POST")
    {
        $form = $this->factory->create(new CreateUrgencyType(), $urgency, array('method' => $method));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $urgency = $form->getData();
            $this->em->persist($urgency);
            $this->em->flush($urgency);
    
            return $urgency;
        }
    
        throw new \Exception($form->getErrorsAsString());
    }
    
    /**
     * Creates/Edit urgency
     *
     * @param Urgency     $urgency
     * @param array         $parameters
     * @param String        $method
     *
     * @return Urgency
     *
     * @throws \Exception
     */
    public function handleHoursUrgency(Urgency $urgency, $request, $method = "POST")
    {
        $form = $this->factory->create(new UrgencyHoursType(), $urgency, array('method' => $method));
        $oldHoursSpent=$urgency->getHoursSpent();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $urgency = $form->getData();
            $urgency->setHoursSpent($urgency->getHoursSpent() + $oldHoursSpent);
            $this->em->persist($urgency);
            $this->em->flush($urgency);

            return $urgency->getHoursSpent()."/".$urgency->getHoursEnd();
        }
    
        throw new \Exception($form->getErrorsAsString());
    }
    
    /**
     * Set urgency state to $state.
     *
     * @param Urgency $urgency
     * @param String $state
     *
     * @return Urgency
     */
    public function moveTo(Urgency $urgency, $state, $sprint=null)
    {
        $urgency->setState($state);
        if($sprint){
            $urgency->setSprint($sprint);
        }
        $this->em->persist($urgency);
        $this->em->flush($urgency);
    }
}