<?php
namespace LogbookREST\EntryBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use LogbookREST\EntryBundle\Form\TagType;
use LogbookREST\EntryBundle\Handler\TagHandler;
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
    public function getHistoriesAction($entry, Request $request, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');

        return $this->container->get('tag.handler')->all($limit, $offset,null,$entry);
    }

    /**
     * @param Tag $tag
     *
     * @View()
     * @return array
     */
    public function getTagAction($entry, $id)
    {
        $tag = $this->getOr404($id);

        return $tag;
    }

    /**
     * @View(template = "EntryBundle:Tag:new-tag.html.twig", statusCode = Codes::HTTP_BAD_REQUEST, templateVar = "form")
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     */
    public function postTagAction($entry, Request $request)
    {
        try {
            $entryObj = $this->get('entry.handler')->get($entry);
            $newTag = $this->get('tag.handler')->post($entryObj, $request);
            $routeOptions = array(
                'id' => $entry,
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('get_entry', $routeOptions, Codes::HTTP_CREATED);
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
    public function putTagAction($entry, Request $request, $id)
    {
        try {
            if (!($tag = $this->container->get('tag.handler')->get($id))) {
                $entryObj = $this->get('entry.handler')->get($entry);
                $statusCode = Codes::HTTP_CREATED;
                $tag = $this->container->get('tag.handler')->post($entryObj, $request);
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
                $entryObj = $this->get('entry.handler')->get($entry);
                $tag = $this->container->get('tag.handler')->put($entryObj, $tag, $request);
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
    public function patchTagAction($entry, Request $request, $id)
    {
        try {
            if (($tag = $this->container->get('tag.handler')->get($id))) {
                $statusCode = Codes::HTTP_ACCEPTED;
                $entryObj = $this->get('entry.handler')->get($entry);
                $tag = $this->container->get('tag.handler')->patch($tag, $request, $entryObj);
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
    public function deleteTagAction($entry, Request $request, $id)
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