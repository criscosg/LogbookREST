<?php
namespace EasyScrumREST\UserBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\View;
use IHorseREST\UserBundle\Entity\Company;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Util\Codes;
use IHorseREST\UserBundle\Form\CompanyType;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Symfony\Component\HttpFoundation\Response;

class CompanyController extends FOSRestController{

    /**
     * @QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing pages.")
     * @QueryParam(name="limit", requirements="\d+", nullable=true, default="20", description="How many pages to return.")
     *
     * @View()
     *
     * @param Request $request      the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getCompaniesAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');

        return $this->container->get('company.handler')->all($limit, $offset);
    }

    /**
     * @param Company $company
     *
     * @View()
     * @return array
     */
    public function getCompanyAction($id)
    {
        $company=$this->getOr404($id);

        return $company;
    }

    /**
     * @View(template = "VeterinaryBundle:Company:newCompany.html.twig", statusCode = Codes::HTTP_BAD_REQUEST, templateVar = "form")
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     */
    public function postCompanyAction(Request $request)
    {
        try {
            $newCompany = $this->get('company.handler')->post($request);

            $routeOptions = array(
                    'id' => $newCompany->getId(),
                    '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('get_company', $routeOptions, Codes::HTTP_CREATED);
        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * @View()
     *
     * @return FormTypeInterface
     */
    public function newCompanyAction()
    {
        return $this->createForm(new CompanyType());
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
     * @throws NotFoundHttpException when company not exist
     */
    public function putCompanyAction(Request $request, $id)
    {
        try {
            if (!($company = $this->container->get('company.handler')->get($id))) {
                $statusCode = Codes::HTTP_CREATED;
                $company = $this->container->get('company.handler')->post($request);
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
                $company = $this->container->get('company.handler')->put($company, $request);
            }
            $response = new Response('Die Klinik wurde erfolgreich gespeichert', $statusCode);

            return $response;
        } catch (\Exception $exception) {

            return $exception->getMessage();
        }
    }

    /**
     * @param Request $request
     * @param int     $id
     * 
     * @View()
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when company not exist
     */
    public function patchCompanyAction(Request $request, $id)
    {
        try {
            if (($company = $this->getOr404($id))) {
                $statusCode = Codes::HTTP_ACCEPTED;
                $company = $this->container->get('company.handler')->patch($company, $request);
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
            }
            $response = new Response('Die Klinik wurde erfolgreich gespeichert', $statusCode);

            return $response;
        } catch (NotFoundHttpException $exception) {

            return array($statusCode=>'Die Klinik wurde erfolgreich gespeichert');
        }
    }

    /**
     * @param Request $request
     * @param int $id
     * 
     * @View()
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when company not exist
     */
    public function deleteCompanyAction(Request $request, $id)
    {
        if (($company = $this->container->get('company.handler')->get($id))) {
            $statusCode = Codes::HTTP_ACCEPTED;
            $company = $this->container->get('company.handler')->delete($company);
        } else {
            $statusCode = Codes::HTTP_NO_CONTENT;
        }
        $response = new Response('Das Klinik wurde ernfernt', $statusCode);

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
        if (!($page = $this->container->get('company.handler')->get($id))) {
            throw new NotFoundHttpException(sprintf('The Company \'%s\' was not found.',$id));
        }

        return $page;
    }

}