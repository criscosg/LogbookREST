<?php
namespace LogbookREST\ImageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use LogbookREST\ImageBundle\Form\Type\ImageType;
use Symfony\Component\HttpFoundation\Request;

class ImageController extends FOSRestController
{
    /**
     * @QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing pages.")
     * @QueryParam(name="limit", requirements="\d+", nullable=true, default="20", description="How many pages to return.")
     * @QueryParam(name="horse", requirements="\d+", nullable=true, description="The horse.")
     *
     * @View()
     *
     * @param Request               $request      the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getImagesAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');
        $horse = $paramFetcher->get('horse');

        return $this->container->get('image.handler')->all($limit, $offset, null, $horse);
    }

    /**
     * @param Horse $image
     *
     * @View()
     * @return array
     */
    public function getImageAction($id)
    {
        $image = $this->getOr404($id);

        return $image->getWebFilePath();
    }

    /**
     * @View(template = "ImageBundle:Image:new-image.html.twig", statusCode = Codes::HTTP_BAD_REQUEST, templateVar = "form")
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     */
    public function postImageAction(Request $request)
    {
        try {
            $newImage = $this->get('image.handler')->post($request);

            $routeOptions = array(
                    'id' => $newImage->getId(),
                    '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('get_image', $routeOptions, Codes::HTTP_CREATED);
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
    public function newImageAction()
    {
        return $this->createForm(new ImageType());
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
     * @throws NotFoundHttpException when Image not exist
     */
    public function putImageAction(Request $request, $id)
    {
        try {
            if (!($image = $this->container->get('image.handler')->get($id))) {
                $statusCode = Codes::HTTP_CREATED;
                $image = $this->container->get('image.handler')->post($request);
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
                $image = $this->container->get('image.handler')->put($image, $request);
            }
    
            $routeOptions = array('id' => $image->getId(), '_format' => $request->get('_format')
            );
    
            return $this->routeRedirectView('get_image', $routeOptions, $statusCode);
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
     * @throws NotFoundHttpException when Image not exist
     */
    public function patchImageAction(Request $request, $id)
    {
        try {
            if (($image = $this->container->get('image.handler')->get($id))) {
                $statusCode = Codes::HTTP_ACCEPTED;
                $image = $this->container->get('image.handler')->patch($image, $request);
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
            }
    
            $routeOptions = array('id' => $image->getId(), '_format' => $request->get('_format'));
    
            return $this->routeRedirectView('get_image', $routeOptions, $statusCode);
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
     * @throws NotFoundHttpException when hohrse not exist
     */
    public function deleteImageAction(Request $request, $id)
    {
        if (($image = $this->container->get('image.handler')->get($id))) {
            $statusCode = Codes::HTTP_ACCEPTED;
            $image = $this->container->get('image.handler')->delete($image);
        } else {
            $statusCode = Codes::HTTP_NO_CONTENT;
        }
    
        $routeOptions = array('_format' => $request->get('_format'));
    
        return $this->routeRedirectView('get_images', $routeOptions, $statusCode);
    }

    /**
     * @QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing pages.")
     * @QueryParam(name="limit", requirements="\d+", nullable=true, default="20", description="How many pages to return.")
     * @QueryParam(name="horse", requirements="\d+", nullable=true, description="The horse.")
     *
     * @View()
     *
     * @param Request               $request      the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getThumbnailsAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');
        $horse = $paramFetcher->get('horse');
    
        return $this->container->get('image.thumbnail.handler')->all($limit, $offset, null, $horse);
    }

    /**
     * @View()
     * @return array
     */
    public function getImageThumbnailAction($id)
    {
        $image = $this->getThumbnailOr404($id);

        return array('image' => $image->getWebFilePath());
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
        if (!($page = $this->container->get('image.handler')->get($id))) {
            throw new NotFoundHttpException(sprintf('The Image \'%s\' was not found.',$id));
        }
    
        return $page;
    }

    /**
     * Fetch the Thumbnail or throw a 404 exception.
     *
     * @param mixed $id
     *
     * @return PageInterface
     *
     * @throws NotFoundHttpException
     */
    protected function getThumbnailOr404($id)
    {
        if (!($page = $this->container->get('image.thumbnail.handler')->get($id))) {
            throw new NotFoundHttpException(sprintf('The Image \'%s\' was not found.',$id));
        }

        return $page;
    }
}