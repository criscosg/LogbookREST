<?php

namespace EasyScrumREST\ProjectBundle\Controller;

use EasyScrumREST\ProjectBundle\Form\BacklogRestType;

use FOS\RestBundle\Controller\FOSRestController;
use EasyScrumREST\ProjectBundle\Form\BacklogType;
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

class BacklogController extends FOSRestController
{
    /**
     * @QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing pages.")
     * @QueryParam(name="limit", requirements="\d+", nullable=true, default="20", description="How many pages to return.")
     * @QueryParam(name="search_backlog", array=true, nullable=true, description="The search.")
     * @View()
     *
     * @param Request               $request      the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getBacklogsAction($salt, Request $request, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');

        return $this->container->get('backlog.handler')->all($salt, $limit, $offset, null);
    }

    /**
     * @param Backlog $backlog
     *
     * @View()
     * @return array
     */
    public function getBacklogAction($project, $salt)
    {
        $backlog = $this->getOr404($salt);

        return $backlog;
    }

    /**
     * @View(template = "ProjectBundle:Project:new-backlog.html.twig", statusCode = Codes::HTTP_BAD_REQUEST, templateVar = "form")
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     */
    public function postBacklogAction($salt, Request $request)
    {
        try {
            $project = $this->container->get('project.handler')->get($salt);
            $newBacklog = $this->get('backlog.handler')->post($request, $project);

            $routeOptions = array(
                'project' => $newBacklog->getProject()->getSalt(),
                'salt' => $newBacklog->getSalt(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('get_project_backlog', $routeOptions, Codes::HTTP_CREATED);
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
    public function newBacklogAction()
    {
        return $this->createForm(new BacklogRestType());
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
     * @throws NotFoundHttpException when backlog not exist
     */
    public function putBacklogAction($salt, Request $request, $id)
    {
        try {
            if (!($backlog = $this->container->get('backlog.handler')->get($id))) {
                $statusCode = Codes::HTTP_CREATED;
                $backlog = $this->container->get('backlog.handler')->post($request);
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
                $backlog = $this->container->get('backlog.handler')->put($backlog, $request);
            }
            $response = new Response('Error', $statusCode);

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
     * @throws NotFoundHttpException when backlog not exist
     */
    public function patchBacklogAction($salt, Request $request, $id)
    {
        try {
            if (($backlog = $this->container->get('backlog.handler')->get($id))) {
                $statusCode = Codes::HTTP_ACCEPTED;
                $backlog = $this->container->get('backlog.handler')->patch($backlog, $request);
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
     * @throws NotFoundHttpException when backlog not exist
     */
    public function deleteBacklogAction($salt, Request $request, $id)
    {
        if (($backlog = $this->container->get('backlog.handler')->get($id))) {
            $statusCode = Codes::HTTP_ACCEPTED;
            $backlog = $this->container->get('backlog.handler')->delete($backlog);
        } else {
            $statusCode = Codes::HTTP_NO_CONTENT;
        }
        $response = new Response('Error', $statusCode);

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
        if (!($page = $this->container->get('backlog.handler')->get($id))) {
            throw new NotFoundHttpException(backlogf('The Backlog \'%s\' was not found.',$id));
        }

        return $page;
    }
}