<?php

namespace EasyScrumREST\SprintBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use EasyScrumREST\SprintBundle\Form\SprintType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Symfony\Component\HttpFoundation\Response;

class SprintController extends FOSRestController
{
    /**
     * @QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing pages.")
     * @QueryParam(name="limit", requirements="\d+", nullable=true, default="20", description="How many pages to return.")
     * @QueryParam(name="search_sprint",array=true, nullable=true, description="The search.")
     * @View()
     *
     * @param Request               $request      the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getSprintsAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');
        $search = $paramFetcher->get('search_sprint');
        if (!is_null($this->getUser()) && ($this->container->get('security.context')->isGranted('ROLE_API_USER'))) {
            $search['company']=$this->getUser()->getCompany()->getId();
        }

        return $this->container->get('sprint.handler')->all($limit, $offset, null, $search);
    }

    /**
     * @param Sprint $sprint
     *
     * @View()
     * @return array
     */
    public function getSprintAction($id)
    {
        $sprint = $this->getOr404($id);

        return $sprint;
    }

    /**
     * @View(template = "SprintBundle:Sprint:new-sprint.html.twig", statusCode = Codes::HTTP_BAD_REQUEST, templateVar = "form")
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     */
    public function postSprintAction(Request $request)
    {
        try {
            $newSprint = $this->get('sprint.handler')->post($request);

            $routeOptions = array(
                'id' => $newSprint->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('get_sprint', $routeOptions, Codes::HTTP_CREATED);
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
    public function newSprintAction()
    {
        return $this->createForm(new SprintType());
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
     * @throws NotFoundHttpException when sprint not exist
     */
    public function putSprintAction(Request $request, $id)
    {
        try {
            if (!($sprint = $this->container->get('sprint.handler')->get($id))) {
                $statusCode = Codes::HTTP_CREATED;
                $sprint = $this->container->get('sprint.handler')->post($request);
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
                $sprint = $this->container->get('sprint.handler')->put($sprint, $request);
            }
            $response = new Response('Der Besitzer des Pferdes wurde erfolgreich gespeichert', $statusCode);

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
     * @throws NotFoundHttpException when sprint not exist
     */
    public function patchSprintAction(Request $request, $id)
    {
        try {
            if (($sprint = $this->container->get('sprint.handler')->get($id))) {
                $statusCode = Codes::HTTP_ACCEPTED;
                $sprint = $this->container->get('sprint.handler')->patch($sprint, $request);
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
            }
            $response = new Response('Der Besitzer des Pferdes wurde erfolgreich gespeichert', $statusCode);

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
     * @throws NotFoundHttpException when sprint not exist
     */
    public function deleteSprintAction(Request $request, $id)
    {
        if (($sprint = $this->container->get('sprint.handler')->get($id))) {
            $statusCode = Codes::HTTP_ACCEPTED;
            $sprint = $this->container->get('sprint.handler')->delete($sprint);
        } else {
            $statusCode = Codes::HTTP_NO_CONTENT;
        }
        $response = new Response('Der Besitzer des Pferdes wurde erfolgreich gelöscht', $statusCode);

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
        if (!($page = $this->container->get('sprint.handler')->get($id))) {
            throw new NotFoundHttpException(sprintf('The Sprint \'%s\' was not found.',$id));
        }

        return $page;
    }
}