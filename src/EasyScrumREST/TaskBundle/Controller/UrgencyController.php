<?php
namespace EasyScrumREST\TaskBundle\Controller;

use EasyScrumREST\TaskBundle\Form\UrgencyType;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\View;
use EasyScrumREST\TaskBundle\Entity\Task;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Response;

class UrgencyController extends FOSRestController{

    /**
     * @QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing pages.")
     * @QueryParam(name="limit", requirements="\d+", nullable=true, default="20", description="How many pages to return.")
     * @QueryParam(name="search_urgencys",array=true, nullable=true, description="The search.")
     * @View()
     *
     * @param Request               $request      the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getUrgenciesAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');
        $search = $paramFetcher->get('search_urgencys');
        if (!is_null($this->getUser()) && ($this->container->get('security.context')->isGranted('ROLE_API_USER'))) {
            $search['company']=$this->getUser()->getCompany()->getId();
        }

        return $this->container->get('urgency.handler')->all($search, $limit, $offset);
    }

    /**
     * @param Urgency $urgency
     *
     * @View()
     * @return array
     */
    public function getUrgencyAction($id)
    {
        $urgency = $this->getOr404BySalt($id);

        return $urgency;
    }

    /**
     * @View(template = "EasyScrumREST:Urgency:newUrgency.html.twig", statusCode = Codes::HTTP_BAD_REQUEST, templateVar = "form")
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     */
    public function postUrgencyAction(Request $request)
    {
        try {
            $newUrgency = $this->get('urgency.handler')->post($request);

            $routeOptions = array(
                    'id' => $newUrgency->getId(),
                    '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('get_urgency', $routeOptions, Codes::HTTP_CREATED);
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
    public function newUrgencyAction()
    {
        return $this->createForm(new UrgencyType());
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
     * @throws NotFoundHttpException when urgency not exist
     */
    public function putUrgencyAction(Request $request, $id)
    {
        try {
            if (!($urgency = $this->container->get('urgency.handler')->getSalt($id))) {
                $statusCode = Codes::HTTP_CREATED;
                $urgency = $this->container->get('urgency.handler')->post($request);
            } else {
                $statusCode = Codes::HTTP_ACCEPTED;
                $urgency = $this->container->get('urgency.handler')->put($urgency, $request);
            }
            $response = new Response('The project has been updated', $statusCode);

            return $response;
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
     * @throws NotFoundHttpException when urgency not exist
     */
    public function patchUrgencyAction(Request $request, $id)
    {
        try {
            if (($urgency = $this->getOr404BySalt($id))) {
                $statusCode = Codes::HTTP_ACCEPTED;
                $urgency = $this->container->get('urgency.handler')->patch($urgency, $request);
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
            }

            $routeOptions = array('id' => $urgency->getId(), '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('get_urgency', $routeOptions, $statusCode);
        } catch (NotFoundHttpException $exception) {

            return $exception->getMessage();
        }
    }

    /**
     *
     * @View()
     * @Post("/urgency-hours/{salt}")
     *
     * @param Request $request
     * @param string     $salt
     *
     *
     * @throws NotFoundHttpException when urgency not exist
     */
    public function urgencyHoursAction(Request $request, $salt)
    {
        try {
            if (($urgency = $this->getOr404BySalt($salt))) {
                if (!is_null($this->getUser()) && ($this->container->get('security.context')->isGranted('ROLE_USER'))) {
                    $statusCode = Codes::HTTP_ACCEPTED;
                    try {
                        $hours=$this->container->get('urgency.handler')->handleHoursUrgency($urgency, $this->getUser(), $request);

                        return new Response($hours, $statusCode);
                    } catch(\Exception $exception) {
                        return $exception->getMessage();
                    }
                } else {
                    $statusCode = Codes::HTTP_NO_CONTENT;
                }
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
            }

            $response = new Response('ok', $statusCode);

            return $response;
        } catch (NotFoundHttpException $exception) {
            return $exception->getMessage();
        }
    }

    /**
     *
     * @View()
     * @Get("/urgency-move/{salt}/{state}")
     *
     * @param Request $request
     * @param int     $salt
     *
     * @throws NotFoundHttpException when urgency not exist
     */
    public function urgencyMoveAction(Request $request, $salt, $state)
    {
        try {
            $urgency = $this->getOr404BySalt($salt);
            if ($urgency) {
                if (!is_null($this->getUser()) && ($this->container->get('security.context')->isGranted('ROLE_USER'))) {
                    $statusCode = Codes::HTTP_ACCEPTED;
                    $this->container->get('urgency.handler')->moveTo($urgency, $state);
                } else {
                    $statusCode = Codes::HTTP_NO_CONTENT;
                }
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
            }

            $response = new Response('ok', $statusCode);

            return $response;
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
     * @throws NotFoundHttpException when urgency not exist
     */
    public function deleteUrgencyAction(Request $request, $id)
    {
        if (($urgency = $this->container->get('urgency.handler')->getSalt($id))) {
            $statusCode = Codes::HTTP_ACCEPTED;
            $urgency = $this->container->get('urgency.handler')->delete($urgency);
        } else {
            $statusCode = Codes::HTTP_NO_CONTENT;
        }
        $routeOptions = array('_format' => $request->get('_format'));
        $response = new Response('ok', $statusCode);

        return $response;
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
        if (!($urgency = $this->container->get('urgency.handler')->get($id))) {
            throw new NotFoundHttpException(sprintf('The Urgency \'%s\' was not found.',$id));
        }

        return $urgency;
    }

    /**
     * Fetch the Page or throw a 404 exception.
     *
     * @param mixed $salt
     *
     * @return PageInterface
     *
     * @throws NotFoundHttpException
     */
    protected function getOr404BySalt($salt)
    {
        if (!($urgency = $this->container->get('urgency.handler')->getSalt($salt))) {
            throw new NotFoundHttpException(sprintf('The Urgency \'%s\' was not found.',$salt));
        }

        return $urgency;
    }

}