<?php

namespace EasyScrumREST\UserBundle\Handler;

use Symfony\Component\HttpFoundation\Request;

use EasyScrumREST\UserBundle\Form\ProfileType;
use EasyScrumREST\UserBundle\Form\NormalUserType;
use EasyScrumREST\UserBundle\Form\RegisterType;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use EasyScrumREST\UserBundle\Form\ApiUserType;
use EasyScrumREST\UserBundle\Handler\ApiUserHandlerInterface;
use Doctrine\ORM\EntityManager;
use EasyScrumREST\UserBundle\Entity\ApiUser;
use Symfony\Component\Form\FormFactoryInterface;
use EasyScrumREST\UserBundle\Form\AdminApiUserType;

class ApiUserHandler
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
     * Create a new ApiUser.
     *
     * @param $request
     *
     * @return ApiUser
     */
    public function create($request, $company)
    {
        $user = new ApiUser();
        $user->setCompany($company);
        $form = $this->factory->create(new NormalUserType(), $user);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->em->persist($user);
            $this->em->flush($user);
    
            return $user;
        }
    
        throw new \Exception('Invalid submitted data');
    }

    /**
     * Edit a profile.
     *
     * @param $request
     *
     * @return ApiUser
     */
    public function editProfile(Request $request,ApiUser $user)
    {
        $form = $this->factory->create(new ProfileType(), $user);
        $oldPass=$user->getPassword();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $req = $request->request->get('profile');
            if($req['password']){
                $encoder = $this->encoderFactory->getEncoder($user);
                $passwordEncoded = $encoder->encodePassword($user->getPassword(), $user->getSalt());
                $user->setPassword($passwordEncoded);
            } else {
                $user->setPassword($oldPass);
            }
            
            $this->em->persist($user);
            $this->em->flush($user);

            $files=$request->files->get('profile');
            if($files['profileImage']['file']){
                $image=$user->getProfileImage();
                $image->setUser($user);
                $image->uploadImage();
                $this->em->persist($image);
                $this->em->flush($image);
            }

            return $user;
        }

        throw new \Exception('Invalid submitted data');
    }
    
    /**
     * Create a new ApiUser.
     *
     * @param $request
     *
     * @return ApiUser
     */
    public function edit($user, $request)
    {
        return $this->processNormalForm($user, $request);
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
        if (count($form->getErrors())==0) {
            if ($method!='POST') {
                $req = $request->request->get('api_user');
                if($req['password']){
                    $encoder = $this->encoderFactory->getEncoder($user);
                    $passwordEncoded = $encoder->encodePassword($user->getPassword(), $user->getSalt());
                    $user->setPassword($passwordEncoded);
                }
            }
            $this->em->persist($user);
            $this->em->flush($user);

            return $user;
        }

        throw new \Exception('Invalid submitted data');
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
    private function processNormalForm(ApiUser $user, $request, $method = "POST")
    {
        $form = $this->factory->create(new NormalUserType(), $user, array('method' => $method));
        $oldPass=$user->getPassword();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $req = $request->request->get('api_user');
            if($req['password']){
                $encoder = $this->encoderFactory->getEncoder($user);
                $passwordEncoded = $encoder->encodePassword($user->getPassword(), $user->getSalt());
                $user->setPassword($passwordEncoded);
            } else {
                $user->setPassword($oldPass);
            }
            $this->em->persist($user);
            $this->em->flush($user);
    
            return $user;
        }
    
        throw new \Exception('Invalid submitted data');
    }
    
    /**
     * Register a user.
     *
     * @param ApiUser       $user
     * @param array         $parameters
     * @param String        $method
     *
     * @return ApiUser
     *
     * @throws \Exception
     */
    public function register(ApiUser $user, $request, $method = "POST")
    {
        $form = $this->factory->create(new RegisterType(), $user, array('method' => $method));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->em->persist($user);
            $this->em->flush($user);
    
            return $user;
        }
    
        throw new \Exception('Invalid submitted data');
    }
}