<?php
namespace LogbookREST\EntryBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\View;
use LogbookREST\EntryBundle\Entity\Entry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Util\Codes;
use LogbookREST\EntryBundle\Form\EntryType;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;

class EntryController extends FOSRestController{

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
    public function getEntriesAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');

        return $this->container->get('entry.handler')->all($limit, $offset);
    }

    /**
     * @param Entry $entry
     *
     * @View()
     * @return array
     */
    public function getEntryAction($id)
    {
        $entry=$this->getOr404($id);

        return $entry;
    }

    /**
     * @View(template = "EntryBundle:Entry:newEntry.html.twig", statusCode = Codes::HTTP_BAD_REQUEST, templateVar = "form")
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     */
    public function postEntryAction(Request $request)
    {
        try {
            $newEntry = $this->get('entry.handler')->post($request);

            $routeOptions = array(
                    'id' => $newEntry->getId(),
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
    public function newEntryAction()
    {
        return $this->createForm(new EntryType());
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
     * @throws NotFoundHttpException when entry not exist
     */
    public function putEntryAction(Request $request, $id)
    {
        try {
            if (!($entry = $this->container->get('entry.handler')->get($id))) {
                $statusCode = Codes::HTTP_CREATED;
                $entry = $this->container->get('entry.handler')->post($request);
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
                $entry = $this->container->get('entry.handler')->put($entry, $request);
            }

            $routeOptions = array('id' => $entry->getId(), '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('get_entry', $routeOptions, $statusCode);
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
     * @throws NotFoundHttpException when entry not exist
     */
    public function patchEntryAction(Request $request, $id)
    {
        try {
            if (($entry = $this->getOr404($id))) {
                $statusCode = Codes::HTTP_ACCEPTED;
                $entry = $this->container->get('entry.handler')->patch($entry, $request);
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
            }

            $routeOptions = array('id' => $entry->getId(), '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('get_entry', $routeOptions, $statusCode);
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
     * @throws NotFoundHttpException when entry not exist
     */
    public function deleteEntryAction(Request $request, $id)
    {
        if (($entry = $this->container->get('entry.handler')->get($id))) {
            $statusCode = Codes::HTTP_ACCEPTED;
            $entry = $this->container->get('entry.handler')->delete($entry);
        } else {
            $statusCode = Codes::HTTP_NO_CONTENT;
        }

        $routeOptions = array('_format' => $request->get('_format'));

        return $this->routeRedirectView('get_entries', $routeOptions, $statusCode);
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
        if (!($page = $this->container->get('entry.handler')->get($id))) {
            throw new NotFoundHttpException(sprintf('The Entry \'%s\' was not found.',$id));
        }

        return $page;
    }

}