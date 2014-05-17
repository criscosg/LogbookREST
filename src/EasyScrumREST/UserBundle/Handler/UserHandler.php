<?php

namespace EasyScrumREST\UserBundle\Handler;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

use EasyScrumREST\UserBundle\Handler\UserHandlerInterface;
use Doctrine\ORM\EntityManager;
use EasyScrumREST\UserBundle\Entity\AdminUser;
use Symfony\Component\Form\FormFactoryInterface;
use EasyScrumREST\UserBundle\Form\AdminUserType;
use Symfony\Component\BrowserKit\Request;

class UserHandler
{
    private $em;
    private $factory;
    private $encoderFactory;
    
    public function __construct(EntityManager $em, FormFactoryInterface $formFactory, EncoderFactory $encoderFactory)
    {
        $this->em = $em;
        $this->factory = $formFactory;
        $this->encoderFactory = $encoderFactory;
    }

    public function get($id)
    {
        return $this->em->getRepository('UserBundle:AdminUser')->find($id);
    }

    /**
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 20, $offset = 0, $orderby = null)
    {
        return $this->em->getRepository('UserBundle:AdminUser')->findBy(array(), $orderby, $limit, $offset);
    }

    /**
     * Create a new User.
     *
     * @param $request
     *
     * @return AdminUser
     */
    public function post($request)
    {
        $user = new AdminUser();

        return $this->processForm($user, $request, 'POST');
    }
    
    /**
     * @param AdminUser $user
     * @param $request
     *
     * @return AdminUser
     */
    public function put(AdminUser $entity, $request)
    {
        return $this->processForm($entity, $request);
    }
    
    /**
     * @param AdminUser $user
     * @param $request
     *
     * @return AdminUser
     */
    public function patch(AdminUser $entity, $request)
    {
        return $this->processForm($entity, $request, 'PATCH');
    }
    
    /**
     * @param AdminUser $user
     *
     * @return AdminUser
     */
    public function delete(AdminUser $entity)
    {
        $this->em->remove($entity);
        $this->em->flush($entity);
    }
    
    /**
     * Processes the form.
     *
     * @param AdminUser     $user
     * @param array         $parameters
     * @param String        $method
     *
     * @return AdminUser
     *
     * @throws \Exception
     */
    private function processForm(AdminUser $entity, $request, $method = "PUT")
    {
        $form = $this->factory->create(new AdminUserType(), $entity, array('method' => $method));
        $form->handleRequest($request);
        if ($form->isValid()) {
            if ($method!='POST') {
                $req = $request->request->get('api_user');
                if($req['password']){
                    $encoder = $this->encoderFactory->getEncoder($entity);
                    $passwordEncoded = $encoder->encodePassword($entity->getPassword(), $entity->getSalt());
                    $entity->setPassword($passwordEncoded);
                }
            }
            $this->em->persist($entity);
            $this->em->flush($entity);

            return $entity;
        }

        throw new \Exception('Invalid submitted data');
    }
}