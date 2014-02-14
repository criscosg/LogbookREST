<?php

namespace LogbookREST\UserBundle\Handler;
use LogbookREST\UserBundle\Form\ApiUserType;

use LogbookREST\UserBundle\Handler\ApiUserHandlerInterface;
use Doctrine\ORM\EntityManager;
use LogbookREST\UserBundle\Entity\ApiUser;
use Symfony\Component\Form\FormFactoryInterface;
use LogbookREST\UserBundle\Form\AdminApiUserType;
use Symfony\Component\BrowserKit\Request;

class ApiUserHandler
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
        return $this->em->getRepository('UserBundle:ApiUser')->find($id);
    }

    /**
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 20, $offset = 0, $orderby = null)
    {
        return $this->em->getRepository('UserBundle:ApiUser')->findBy(array(), $orderby, $limit, $offset);
    }

    /**
     * Create a new ApiUser.
     *
     * @param $request
     *
     * @return ApiUser
     */
    public function post($request)
    {
        $user = new ApiUser();

        return $this->processForm($user, $request, 'POST');
    }
    
    /**
     * @param ApiUser $user
     * @param $request
     *
     * @return ApiUser
     */
    public function put(ApiUser $user, $request)
    {
        return $this->processForm($user, $request);
    }
    
    /**
     * @param ApiUser $user
     * @param $request
     *
     * @return ApiUser
     */
    public function patch(ApiUser $user, $request)
    {
        return $this->processForm($user, $request, 'PATCH');
    }
    
    /**
     * @param ApiUser $user
     *
     * @return ApiUser
     */
    public function delete(ApiUser $user)
    {
        $this->em->remove($user);
        $this->em->flush($user);
    }
    
    /**
     * Processes the form.
     *
     * @param ApiUser     $user
     * @param array         $parameters
     * @param String        $method
     *
     * @return ApiUser
     *
     * @throws \Exception
     */
    private function processForm(ApiUser $user, $request, $method = "PUT")
    {
        $form = $this->factory->create(new ApiUserType(), $user, array('method' => $method));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $user = $form->getData();
            $this->em->persist($user);
            $this->em->flush($user);

            return $user;
        }

        throw new \Exception('Invalid submitted data');
    }
}