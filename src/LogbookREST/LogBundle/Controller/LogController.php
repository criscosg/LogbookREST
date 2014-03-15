<?php

namespace LogbookREST\LogBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use LogbookREST\LogBundle\Form\LogType;
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

class LogController extends FOSRestController
{
    /**
     * @QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing pages.")
     * @QueryParam(name="limit", requirements="\d+", nullable=true, default="20", description="How many pages to return.")
     * @QueryParam(name="search_log",array=true, nullable=true, description="The search.")
     * @View()
     *
     * @param Request               $request      the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getLogsAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');
        $search = $paramFetcher->get('search_log');
        if (!is_null($this->getUser()) && ($this->container->get('security.context')->isGranted('ROLE_USER'))) {
            $search['user_id']=$this->getUser()->getId();
        }

        return $this->container->get('log.handler')->all($limit, $offset, null, $search);
    }

    /**
     * @param Log $log
     *
     * @View()
     * @return array
     */
    public function getLogAction($id)
    {
        $log = $this->getOr404($id);

        return $log;
    }

    /**
     * @View(template = "LogBundle:Log:new-log.html.twig", statusCode = Codes::HTTP_BAD_REQUEST, templateVar = "form")
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     */
    public function postLogAction(Request $request)
    {
        try {
            $newLog = $this->get('log.handler')->post($request);

            $routeOptions = array(
                'id' => $newLog->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('get_log', $routeOptions, Codes::HTTP_CREATED);
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
    public function newLogAction()
    {
        return $this->createForm(new LogType());
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
     * @throws NotFoundHttpException when log not exist
     */
    public function putLogAction(Request $request, $id)
    {
        try {
            if (!($log = $this->container->get('log.handler')->get($id))) {
                $statusCode = Codes::HTTP_CREATED;
                $log = $this->container->get('log.handler')->post($request);
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
                $log = $this->container->get('log.handler')->put($log, $request);
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
     * @throws NotFoundHttpException when log not exist
     */
    public function patchLogAction(Request $request, $id)
    {
        try {
            if (($log = $this->container->get('log.handler')->get($id))) {
                $statusCode = Codes::HTTP_ACCEPTED;
                $log = $this->container->get('log.handler')->patch($log, $request);
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
     * @throws NotFoundHttpException when log not exist
     */
    public function deleteLogAction(Request $request, $id)
    {
        if (($log = $this->container->get('log.handler')->get($id))) {
            $statusCode = Codes::HTTP_ACCEPTED;
            $log = $this->container->get('log.handler')->delete($log);
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
        if (!($page = $this->container->get('log.handler')->get($id))) {
            throw new NotFoundHttpException(sprintf('The Log \'%s\' was not found.',$id));
        }

        return $page;
    }
}