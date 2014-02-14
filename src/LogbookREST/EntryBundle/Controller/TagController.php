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
    public function getHistoriesAction($horse, Request $request, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');

        return $this->container->get('tag.handler')->all($limit, $offset,null,$horse);
    }

    /**
     * @param Tag $tag
     *
     * @View()
     * @return array
     */
    public function getTagAction($horse, $id)
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
    public function postTagAction($horse, Request $request)
    {
        try {
            $horseObj = $this->get('horse.handler')->get($horse);
            $newTag = $this->get('tag.handler')->post($horseObj, $request);
            $routeOptions = array(
                'id' => $horse,
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('get_horse', $routeOptions, Codes::HTTP_CREATED);
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
    public function putTagAction($horse, Request $request, $id)
    {
        try {
            if (!($tag = $this->container->get('tag.handler')->get($id))) {
                $horseObj = $this->get('horse.handler')->get($horse);
                $statusCode = Codes::HTTP_CREATED;
                $tag = $this->container->get('tag.handler')->post($horseObj, $request);
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
                $horseObj = $this->get('horse.handler')->get($horse);
                $tag = $this->container->get('tag.handler')->put($horseObj, $tag, $request);
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
    public function patchTagAction($horse, Request $request, $id)
    {
        try {
            if (($tag = $this->container->get('tag.handler')->get($id))) {
                $statusCode = Codes::HTTP_ACCEPTED;
                $horseObj = $this->get('horse.handler')->get($horse);
                $tag = $this->container->get('tag.handler')->patch($tag, $request, $horseObj);
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
    public function deleteTagAction($horse, Request $request, $id)
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