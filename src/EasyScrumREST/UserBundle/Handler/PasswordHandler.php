<?php

namespace EasyScrumREST\UserBundle\Handler;

use EasyScrumREST\UserBundle\Form\PasswordType;

use EasyScrumREST\UserBundle\Form\RecoverPasswordType;
use EasyScrumREST\UserBundle\Entity\RecoverPassword;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

class PasswordHandler
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

    public function get($salt)
    {
        if ($salt) {
            return $this->em->getRepository('UserBundle:RecoverPassword')->findOneBy(array('salt' => $salt));
        }
        return $this->em->getRepository('UserBundle:User')->find($salt);
    }

    /**
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 20, $offset = 0, $orderby = null)
    {
        return $this->em->getRepository('UserBundle:User')->findBy(array(), $orderby, $limit, $offset);
    }

    /**
     * Create a new RecoverPassword.
     *
     * @param $request
     *
     * @return RecoverPassword
     */
    public function post($request)
    {
        $recover = new RecoverPassword();

        return $this->processForm($recover, $request, 'POST');
    }
    
    /**
     * @param RecoverPassword $entity
     * @param $request
     *
     * @return RecoverPassword
     */
    public function put(RecoverPassword $entity, $request)
    {
        return $this->processForm($entity, $request);
    }
    
    /**
     * @param RecoverPassword $entity
     * @param $request
     *
     * @return RecoverPassword
     */
    public function patch(RecoverPassword $entity, $request)
    {
        return $this->processForm($entity, $request, 'PATCH');
    }
    
    /**
     * @param RecoverPassword $entity
     *
     * @return RecoverPassword
     */
    public function delete(RecoverPassword $entity)
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
    private function processForm(RecoverPassword $entity, $request, $method = "PUT")
    {
        $form = $this->factory->create(new RecoverPasswordType(), $entity, array('method' => $method));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $salt = md5(time());
            $entity->setSalt($salt);
            $entity->setDateRequest(new \DateTime('now'));
            $this->em->persist($entity);
            $this->em->flush($entity);

            return $entity;
        }

        throw new \Exception('Invalid submitted data');
    }
    
    public function changePassword($request, $recover, $method = "POST")
    {
        $form = $this->factory->create(new PasswordType(), null, array('method' => $method));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            $user = $this->em->getRepository('UserBundle:ApiUser')->findOneBy(array('email'=>$recover->getEmail()));
            if($user) {
                $encoder = $this->encoderFactory->getEncoder($user);
                $passwordEncoded = $encoder->encodePassword($data['password'], $user->getSalt());
                $user->setPassword($passwordEncoded);
                $this->em->persist($user);
                $this->em->flush($user);

                return $user;
            }
        }
        
        throw new \Exception('Invalid submitted data');
    }
}