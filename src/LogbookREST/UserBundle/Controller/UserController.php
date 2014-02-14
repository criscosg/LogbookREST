<?php
namespace LogbookREST\UserBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\View;
use LogbookREST\UserBundle\Entity\User;
use LogbookREST\UserBundle\Entity\AdminUser;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Util\Codes;
use LogbookREST\UserBundle\Form\AdminUserType;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;

class UserController extends FOSRestController{
    
    /**
     * @QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing pages.")
     * @QueryParam(name="limit", requirements="\d+", nullable=true, default="20", description="How many pages to return.")
     *
     * @View()
     *
     * @param Request               $request      the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getUsersAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');

        return $this->container->get('user.handler')->all($limit, $offset);
    }

    /**
    * @param AdminUser $user
    * 
    * @View()
    * @return array
     */
    public function getUserAction($id)
    {
        $user=$this->getOr404($id);

        return $user;
    }

    /**
    * @View(template = "UserBundle:AdminUser:new-admin.html.twig", statusCode = Codes::HTTP_BAD_REQUEST, templateVar = "form")
    *
    * @param Request $request the request object
    *
    * @return FormTypeInterface|View
    */
    public function postUserAction(Request $request)
    {
        try {
            $newUser = $this->get('user.handler')->post($request);

            $routeOptions = array(
                    'id' => $newUser->getId(),
                    '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('get_user', $routeOptions, Codes::HTTP_CREATED);
        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
    }
    
    /**
     *
     * @View()
     *
     * @return FormTypeInterface
     */
    public function newUserAction()
    {
        return $this->createForm(new AdminUserType());
    }
    
    /**
     *
     * @View()
     *
     * @param Request $request
     * @param int     $id
     *
     * @return View
     *
     * @throws NotFoundHttpException when user not exist
     */
    public function putUserAction(Request $request, $id)
    {
        try {
            if (!($user = $this->container->get('user.handler')->get($id))) {
                $statusCode = Codes::HTTP_CREATED;
                $user = $this->container->get('user.handler')->post($request);
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
                $user = $this->container->get('user.handler')->put($user, $request);
            }

            $routeOptions = array('id' => $user->getId(), '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('get_user', $routeOptions, $statusCode);
        } catch (\Exception $exception) {

            return $exception->getMessage();
        }
    }

    /**
     *
     * @View()
     *
     * @param Request $request
     * @param int     $id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when user not exist
     */
    public function patchUserAction(Request $request, $id)
    {
        try {
            if (($user = $this->getOr404($id))) {
                $statusCode = Codes::HTTP_ACCEPTED;
                $user = $this->container->get('user.handler')->patch($user, $request);
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
            }
    
            $routeOptions = array('id' => $user->getId(), '_format' => $request->get('_format')
            );
    
            return $this->routeRedirectView('get_user', $routeOptions, $statusCode);
        } catch (NotFoundHttpException $exception) {
    
            return $exception->getMessage();
        }
    }

    /**
     *
     * @View()
     *
     * @param Request $request
     * @param int     $id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when user not exist
     */
    public function deleteUserAction(Request $request, $id)
    {
        if (($user = $this->container->get('user.handler')->get($id))) {
            $statusCode = Codes::HTTP_ACCEPTED;
            $user = $this->container->get('user.handler')->delete($user);
        } else {
            $statusCode = Codes::HTTP_NO_CONTENT;
        }

        $routeOptions = array('_format' => $request->get('_format'));

        return $this->routeRedirectView('get_users', $routeOptions, $statusCode);
    }
    
    /**
     * Fetch the Page or throw a 404 exception.
     *
     * @param mixed $id
     *
     * @return PageInterface
     *
     * @throws NotFoundHttpException
     */
    protected function getOr404($id)
    {
        if (!($page = $this->container->get('user.handler')->get($id))) {
            throw new NotFoundHttpException(sprintf('The User \'%s\' was not found.',$id));
        }
    
        return $page;
    }

}