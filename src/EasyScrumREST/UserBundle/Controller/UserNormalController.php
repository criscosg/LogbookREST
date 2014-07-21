<?php
namespace EasyScrumREST\UserBundle\Controller;

use EasyScrumREST\UserBundle\Form\NormalUserType;

use EasyScrumREST\UserBundle\Form\ApiUserType;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use EasyScrumREST\UserBundle\Entity\ApiUser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use EasyScrumREST\UserBundle\Form\RegisterType;
use EasyScrumREST\FrontendBundle\Controller\EasyScrumController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class UserNormalController extends EasyScrumController
{
    public function registerAction()
    {
        $user = new ApiUser();
        $user->setRoles('ROLE_SCRUM_MASTER');
        $request=$this->getRequest();
        $form = $this->createForm(new RegisterType(), $user);
        if($request->getMethod()=='POST'){
            $newApiUser = $this->get('api.user.handler')->register( $user, $request);
            if ($newApiUser) {
                $token = new UsernamePasswordToken($newApiUser,
                        $newApiUser->getPassword(), 'users',
                        $newApiUser->getRoles());
                $this->container->get('security.context')->setToken($token);

                return $this->redirect($this->generateUrl('home'));
            }
        }

        return $this->render('UserBundle:User:register.html.twig', array('form'=>$form->createView()));
    }
    
    public function listApiUsersAction()
    {
        $users=$this->getUser()->getCompany()->getUsers();
        
        return $this->render('UserBundle:ApiUser:index.html.twig', array('api_users'=>$users));
    }
    
    /**
     * @ParamConverter("user", class="UserBundle:ApiUser")
     */
    public function apiUserShowAction($user)
    {
        return $this->render('UserBundle:ApiUser:view.html.twig', array('api_user' => $user));
    }
    
    public function newApiUserAction()
    {
        $form = $this->createForm(new NormalUserType());
        $request=$this->getRequest();
        if($request->getMethod()=='POST'){
            $newApiUser = $this->get('api.user.handler')->create($request);
            if($newApiUser) {
                return $this->redirect($this->generateUrl('api_users_list'));
            }
        }
    
        return $this->render('UserBundle:ApiUser:create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @ParamConverter("user", class="UserBundle:ApiUser")
     */
    public function editApiUserAction($user)
    {
        $form = $form = $this->createForm(new NormalUserType(), $user);
        $request=$this->getRequest();
        if($request->getMethod()=='POST'){
            $apiUser = $this->container->get('api.user.handler')->edit($user, $request);
            if($apiUser) {
                return $this->redirect($this->generateUrl('api_users_list'));
            }
        }
    
        return $this->render('UserBundle:ApiUser:create.html.twig', array('form' => $form->createView(),'edition' => true, 'user' => $user));
    }
    
    /**
     * @ParamConverter("user", class="UserBundle:ApiUser")
     */
    public function deleteApiUserAction($user)
    {
        $this->container->get('api.user.handler')->delete($user);
    
        return $this->redirect($this->generateUrl('api_users_list'));
    }

}