<?php
namespace EasyScrumREST\TaskBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use EasyScrumREST\TaskBundle\Form\TagType;
use EasyScrumREST\TaskBundle\Handler\TagHandler;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;

class TagController extends FOSRestController
{
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
    public function getHistoriesAction($task, Request $request, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');

        return $this->container->get('tag.handler')->all($limit, $offset,null,$task);
    }

    /**
     * @param Tag $tag
     *
     * @View()
     * @return array
     */
    public function getTagAction($task, $id)
    {
        $tag = $this->getOr404($id);

        return $tag;
    }

    /**
     * @View(template = "EasyScrumREST:Tag:new-tag.html.twig", statusCode = Codes::HTTP_BAD_REQUEST, templateVar = "form")
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     */
    public function postTagAction($task, Request $request)
    {
        try {
            $taskObj = $this->get('task.handler')->get($task);
            $newTag = $this->get('tag.handler')->post($taskObj, $request);
            $routeOptions = array(
                'id' => $task,
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('get_task', $routeOptions, Codes::HTTP_CREATED);
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
    public function newTagAction()
    {
        return $this->createForm(new TagType());
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
     * @throws NotFoundHttpException when Tag not exist
     */
    public function putTagAction($task, Request $request, $id)
    {
        try {
            if (!($tag = $this->container->get('tag.handler')->get($id))) {
                $taskObj = $this->get('task.handler')->get($task);
                $statusCode = Codes::HTTP_CREATED;
                $tag = $this->container->get('tag.handler')->post($taskObj, $request);
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
                $taskObj = $this->get('task.handler')->get($task);
                $tag = $this->container->get('tag.handler')->put($taskObj, $tag, $request);
            }
            $response = new Response('Das Pferd wurde erfolgreich gespeichert', $statusCode);

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
     * @throws NotFoundHttpException when Tag not exist
     */
    public function patchTagAction($task, Request $request, $id)
    {
        try {
            if (($tag = $this->container->get('tag.handler')->get($id))) {
                $statusCode = Codes::HTTP_ACCEPTED;
                $taskObj = $this->get('task.handler')->get($task);
                $tag = $this->container->get('tag.handler')->patch($tag, $request, $taskObj);
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
            }
            $response = new Response('Das Pferd wurde erfolgreich gespeichert', $statusCode);

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
     * @throws NotFoundHttpException when tag not exist
     */
    public function deleteTagAction($task, Request $request, $id)
    {
        if (($tag = $this->container->get('tag.handler')->get($id))) {
            $statusCode = Codes::HTTP_ACCEPTED;
            $tag = $this->container->get('tag.handler')->delete($tag);
        } else {
            $statusCode = Codes::HTTP_NO_CONTENT;
        }
        $response = new Response('Das Pferd wurde erfolgreich gespeichert', $statusCode);

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
        if (!($page = $this->container->get('tag.handler')->get($id))) {
            throw new NotFoundHttpException(sprintf('The Tag \'%s\' was not found.',$id));
        }

        return $page;
    }
}