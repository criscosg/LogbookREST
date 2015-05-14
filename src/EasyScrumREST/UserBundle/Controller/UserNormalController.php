<?php
namespace EasyScrumREST\UserBundle\Controller;

use EasyScrumREST\UserBundle\Entity\RecoverPassword;
use EasyScrumREST\UserBundle\Form\PasswordType;
use EasyScrumREST\UserBundle\Form\RecoverPasswordType;
use EasyScrumREST\UserBundle\Event\UserEvent;
use EasyScrumREST\UserBundle\Event\UserEvents;
use EasyScrumREST\UserBundle\Form\SettingsType;
use EasyScrumREST\UserBundle\Form\ProfileType;
use EasyScrumREST\UserBundle\Form\NormalUserType;
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
        $user->setRole('ROLE_SCRUM_MASTER');
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
        $users = $this->getUser()->getCompany()->getUsers();
        
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
        $company=$this->getUser()->getCompany();
        $form = $this->createForm(new NormalUserType());
        $request=$this->getRequest();
        if($request->getMethod()=='POST'){
            $newApiUser = $this->get('api.user.handler')->create($request, $company);
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
    
    public function editProfileAction()
    {
        $user=$this->getUser();
        $form = $form = $this->createForm(new ProfileType(), $user);
        $request=$this->getRequest();
        if($request->getMethod()=='POST'){
            $apiUser = $this->container->get('api.user.handler')->editProfile($request, $user);
            if($apiUser) {
                return $this->redirect($this->generateUrl('api_users_list'));
            }
        }
    
        return $this->render('UserBundle:ApiUser:profile.html.twig', array('form' => $form->createView(),'edition' => true, 'user' => $user));
    }
    
    public function settingsAction()
    {
        $user=$this->getUser();
        $company=$user->getCompany();
        $form = $form = $this->createForm(new SettingsType(), $company);
        $request=$this->getRequest();
        if($request->getMethod()=='POST'){
            $apiUser = $this->container->get('company.handler')->settings($company, $request);
            if($apiUser) {
                return $this->redirect($this->generateUrl('api_users_list'));
            }
        }
    
        return $this->render('UserBundle:ApiUser:settings.html.twig', array('form' => $form->createView(), 'user' => $user));
    }

    /**
     * @ParamConverter("user", class="UserBundle:ApiUser")
     */
    public function deleteApiUserAction($user)
    {
        $this->container->get('api.user.handler')->delete($user);
    
        return $this->redirect($this->generateUrl('api_users_list'));
    }
    
    public function recoverPasswordAction(Request $request)
    {
        $form = $this->createForm(new RecoverPasswordType());
        if ($request->getMethod() == 'POST') {
            $newRecoveryPassword = $this->get('password.handler')->post($request);
            $userEvent = new UserEvent($newRecoveryPassword);
            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->dispatch(UserEvents::RECOVER_PASSWORD, $userEvent);

            return $this->render('UserBundle:Password:forgotPasswordInstructions.html.twig', array('email'=>$newRecoveryPassword->getEmail()));
        }

        return $this->render('UserBundle:Password:recoverPassword.html.twig', array('formPass' => $form->createView()));
    }
    
    /**
     * @ParamConverter("recover", class="UserBundle:RecoverPassword")
     */
    public function ChangePasswordAction(Request $request, RecoverPassword $recover)
    {
        $form = $this->createForm(new PasswordType());
        $user = $this->getDoctrine()->getRepository('UserBundle:ApiUser')->findOneBy(array('email'=>$recover->getEmail()));
        if ($request->getMethod() == 'POST') {
            $user = $this->get('password.handler')->changePassword($request, $recover);
            if($user) {
                $this->resetToken($user);
                return $this->redirect($this->generateUrl('home'));
            } else {
                $this->setTranslatedFlashMessage('An error ocurred while trying to change your password.');
            }
        }

        return $this->render('UserBundle:Password:newPassword.html.twig', array('form' => $form->createView(), 'saltform'=>$recover->getSalt(), 'user'=>$user));
    }

}