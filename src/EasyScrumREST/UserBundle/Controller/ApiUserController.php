<?php
namespace EasyScrumREST\UserBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\View;
use EasyScrumREST\UserBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Util\Codes;
use EasyScrumREST\UserBundle\Form\AdminUserType;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use EasyScrumREST\UserBundle\Form\ApiUserType;

class ApiUserController extends FOSRestController{
    
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
    public function getApiusersAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');

        return $this->container->get('api.user.handler')->all($limit, $offset);
    }

    /**
    * @param ApiUser $apiUser
    * 
    * @View()
    * @return array
     */
    public function getApiuserAction($id)
    {
        $apiUser=$this->getOr404($id);

        return $apiUser;
    }

    /**
    * @View(template = "ApiUserBundle:ApiUser:new-admin.html.twig", statusCode = Codes::HTTP_BAD_REQUEST, templateVar = "form")
    *
    * @param Request $request the request object
    *
    * @return FormTypeInterface|View
    */
    public function postApiuserAction(Request $request)
    {
        try {
            $newApiUser = $this->get('api.user.handler')->post($request);

            $routeOptions = array(
                    'id' => $newApiUser->getId(),
                    '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('get_apiusers', $routeOptions, Codes::HTTP_CREATED);
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
    public function newApiuserAction()
    {
        return $this->createForm(new ApiUserType());
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
     * @throws NotFoundHttpException when apiUser not exist
     */
    public function putApiuserAction(Request $request, $id)
    {
        try {
            if (!($apiUser = $this->container->get('api.user.handler')->get($id))) {
                $statusCode = Codes::HTTP_CREATED;
                $apiUser = $this->container->get('api.user.handler')->post($request);
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
                $apiUser = $this->container->get('api.user.handler')->put($apiUser, $request);
            }

            $routeOptions = array('id' => $apiUser->getId(), '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('get_apiusers', $routeOptions, $statusCode);
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
     * @throws NotFoundHttpException when apiUser not exist
     */
    public function patchApiuserAction(Request $request, $id)
    {
        try {
            if (($apiUser = $this->getOr404($id))) {
                $statusCode = Codes::HTTP_ACCEPTED;
                $apiUser = $this->container->get('api.user.handler')->patch($apiUser, $request);
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
            }
    
            $routeOptions = array('id' => $apiUser->getId(), '_format' => $request->get('_format')
            );
    
            return $this->routeRedirectView('get_apiusers', $routeOptions, $statusCode);
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
     * @throws NotFoundHttpException when apiUser not exist
     */
    public function deleteApiuserAction(Request $request, $id)
    {
        if (($apiUser = $this->container->get('api.user.handler')->get($id))) {
            $statusCode = Codes::HTTP_ACCEPTED;
            $apiUser = $this->container->get('api.user.handler')->delete($apiUser);
        } else {
            $statusCode = Codes::HTTP_NO_CONTENT;
        }

        $routeOptions = array('_format' => $request->get('_format'));

        return $this->routeRedirectView('get_apiusers', $routeOptions, $statusCode);
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
        if (!($page = $this->container->get('api.user.handler')->get($id))) {
            throw new NotFoundHttpException(sprintf('The ApiUser \'%s\' was not found.',$id));
        }
    
        return $page;
    }

}