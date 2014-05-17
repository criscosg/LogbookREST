<?php

namespace IHorseREST\VeterinaryBundle\Handler;
use IHorseREST\VeterinaryBundle\Handler\CompanyHandlerInterface;
use Doctrine\ORM\EntityManager;
use IHorseREST\VeterinaryBundle\Entity\Company;
use Symfony\Component\Form\FormFactoryInterface;
use IHorseREST\VeterinaryBundle\Form\CompanyType;
use Symfony\Component\BrowserKit\Request;

class CompanyHandler
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
        return $this->em->getRepository('VeterinaryBundle:Company')->find($id);
    }

    /**
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 20, $offset = 0, $orderby = null)
    {
        return $this->em->getRepository('VeterinaryBundle:Company')->findBy(array(), $orderby, $limit, $offset);
    }

    /**
     * Create a new Company.
     *
     * @param $request
     *
     * @return Company
     */
    public function post($request)
    {
        $company = new Company();

        return $this->processForm($company, $request, 'POST');
    }
    
    /**
     * @param Company $company
     * @param $request
     *
     * @return Company
     */
    public function put(Company $company, $request)
    {
        return $this->processForm($company, $request);
    }
    
    /**
     * @param Company $company
     * @param $request
     *
     * @return Company
     */
    public function patch(Company $company, $request)
    {
        return $this->processForm($company, $request, 'PATCH');
    }
    
    /**
     * @param Company $company
     *
     * @return Company
     */
    public function delete(Company $company)
    {
        $this->em->remove($company);
        $this->em->flush($company);
    }
    
    /**
     * Processes the form.
     *
     * @param Company     $company
     * @param array         $parameters
     * @param String        $method
     *
     * @return Company
     *
     * @throws \Exception
     */
    private function processForm(Company $company, $request, $method = "PUT")
    {
        $form = $this->factory->create(new CompanyType(), $company, array('method' => $method));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $company = $form->getData();
            $this->em->persist($company);
            $this->em->flush($company);

            return $company;
        }

        throw new \Exception('Invalid submitted data');
    }
}