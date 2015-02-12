<?php

namespace EasyScrumREST\ProjectBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use EasyScrumREST\ProjectBundle\Form\ProjectType;
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

class ProjectRestController extends FOSRestController
{
    /**
     * @QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing pages.")
     * @QueryParam(name="limit", requirements="\d+", nullable=true, default="20", description="How many pages to return.")
     * @QueryParam(name="search_project", array=true, nullable=true, description="The search.")
     * @View()
     *
     * @param Request               $request      the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getProjectsAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');
        $search = $paramFetcher->get('search_project');
        if (!is_null($this->getUser()) && ($this->container->get('security.context')->isGranted('ROLE_API_USER'))) {
            $search['company']=$this->getUser()->getCompany()->getId();
        }

        return $this->container->get('project.handler')->all($limit, $offset, null, $search);
    }

    /**
     * @param Project $project
     *
     * @View()
     * @return array
     */
    public function getProjectAction($id)
    {
        $project = $this->getOr404($id);

        return $project;
    }

    /**
     * @View(template = "ProjectBundle:Project:new-project.html.twig", statusCode = Codes::HTTP_BAD_REQUEST, templateVar = "form")
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     */
    public function postProjectAction(Request $request)
    {
        try {
            $newProject = $this->get('project.handler')->post($request);

            $routeOptions = array(
                'id' => $newProject->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('get_project', $routeOptions, Codes::HTTP_CREATED);
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
    public function newProjectAction()
    {
        return $this->createForm(new ProjectType());
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
     * @throws NotFoundHttpException when project not exist
     */
    public function putProjectAction(Request $request, $id)
    {
        try {
            if (!($project = $this->container->get('project.handler')->get($id))) {
                $statusCode = Codes::HTTP_CREATED;
                $project = $this->container->get('project.handler')->post($request);
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
                $project = $this->container->get('project.handler')->put($project, $request);
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
     * @throws NotFoundHttpException when project not exist
     */
    public function patchProjectAction(Request $request, $id)
    {
        try {
            if (($project = $this->container->get('project.handler')->get($id))) {
                $statusCode = Codes::HTTP_ACCEPTED;
                $project = $this->container->get('project.handler')->patch($project, $request);
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
     * @throws NotFoundHttpException when project not exist
     */
    public function deleteProjectAction(Request $request, $id)
    {
        if (($project = $this->container->get('project.handler')->get($id))) {
            $statusCode = Codes::HTTP_ACCEPTED;
            $project = $this->container->get('project.handler')->delete($project);
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
        if (!($page = $this->container->get('project.handler')->get($id))) {
            throw new NotFoundHttpException(projectf('The Project \'%s\' was not found.',$id));
        }

        return $page;
    }
}