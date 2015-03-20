<?php
namespace EasyScrumREST\TaskBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\View;
use EasyScrumREST\TaskBundle\Entity\Task;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Util\Codes;
use EasyScrumREST\TaskBundle\Form\TaskType;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends FOSRestController{

    /**
     * @QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing pages.")
     * @QueryParam(name="limit", requirements="\d+", nullable=true, default="20", description="How many pages to return.")
     * @QueryParam(name="search_tasks",array=true, nullable=true, description="The search.")
     * @View()
     *
     * @param Request               $request      the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getTasksAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');
        $search = $paramFetcher->get('search_tasks');
        if (!is_null($this->getUser()) && ($this->container->get('security.context')->isGranted('ROLE_API_USER'))) {
            $search['company']=$this->getUser()->getCompany()->getId();
        }

        return $this->container->get('task.handler')->all($search, $limit, $offset);
    }

    /**
     * @param Task $task
     *
     * @View()
     * @return array
     */
    public function getTaskAction($id)
    {
        $task=$this->getOr404BySalt($id);

        return $task;
    }

    /**
     * @View(template = "EasyScrumREST:Task:newTask.html.twig", statusCode = Codes::HTTP_BAD_REQUEST, templateVar = "form")
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     */
    public function postTaskAction(Request $request)
    {
        try {
            $newTask = $this->get('task.handler')->post($request);

            $routeOptions = array(
                    'id' => $newTask->getId(),
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
    public function newTaskAction()
    {
        return $this->createForm(new TaskType());
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
     * @throws NotFoundHttpException when task not exist
     */
    public function putTaskAction(Request $request, $id)
    {
        try {
            if (!($task = $this->container->get('task.handler')->getSalt($id))) {
                $statusCode = Codes::HTTP_CREATED;
                $task = $this->container->get('task.handler')->post($request);
            } else {
                $statusCode = Codes::HTTP_ACCEPTED;
                $task = $this->container->get('task.handler')->put($task, $request);
            }
            $response = new Response('The project has been updated', $statusCode);

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
     * @throws NotFoundHttpException when task not exist
     */
    public function patchTaskAction(Request $request, $id)
    {
        try {
            if (($task = $this->getOr404BySalt($id))) {
                $statusCode = Codes::HTTP_ACCEPTED;
                $task = $this->container->get('task.handler')->patch($task, $request);
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
            }

            $routeOptions = array('id' => $task->getId(), '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('get_task', $routeOptions, $statusCode);
        } catch (NotFoundHttpException $exception) {

            return $exception->getMessage();
        }
    }

    /**
     *
     * @View()
     * @Post("/task-hours/{salt}")
     *
     * @param Request $request
     * @param int     $id
     *
     *
     * @throws NotFoundHttpException when task not exist
     */
    public function taskHoursAction(Request $request, $salt)
    {
        try {
            if (($task = $this->getOr404BySalt($salt))) {
                if (!is_null($this->getUser()) && ($this->container->get('security.context')->isGranted('ROLE_USER'))) {
                    $statusCode = Codes::HTTP_ACCEPTED;
                    $this->container->get('task.handler')->handleHoursTask($task, $this->getUser(), $request);
                } else {
                    $statusCode = Codes::HTTP_NO_CONTENT;
                }
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
            }

            return "ok";
        } catch (NotFoundHttpException $exception) {

            return $exception->getMessage();
        }
    }

    /**
     *
     * @View()
     * @Get("/task-move/{salt}/{state}")
     *
     * @param Request $request
     * @param int     $salt
     *
     * @throws NotFoundHttpException when task not exist
     */
    public function taskMoveAction(Request $request, $salt, $state)
    {
        try {
            $task = $this->getOr404BySalt($salt);
            if ($task) {
                if (!is_null($this->getUser()) && ($this->container->get('security.context')->isGranted('ROLE_USER'))) {
                    $statusCode = Codes::HTTP_ACCEPTED;
                    $this->container->get('task.handler')->moveTo($task, $state);
                } else {
                    $statusCode = Codes::HTTP_NO_CONTENT;
                }
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
            }

            return 'ok';
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
     * @throws NotFoundHttpException when task not exist
     */
    public function deleteTaskAction(Request $request, $id)
    {
        if (($task = $this->container->get('task.handler')->get($id))) {
            $statusCode = Codes::HTTP_ACCEPTED;
            $task = $this->container->get('task.handler')->delete($task);
        } else {
            $statusCode = Codes::HTTP_NO_CONTENT;
        }

        $routeOptions = array('_format' => $request->get('_format'));

        return $this->routeRedirectView('get_tasks', $routeOptions, $statusCode);
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
        if (!($task = $this->container->get('task.handler')->get($id))) {
            throw new NotFoundHttpException(sprintf('The Task \'%s\' was not found.',$id));
        }

        return $task;
    }

    /**
     * Fetch the Page or throw a 404 exception.
     *
     * @param mixed $salt
     *
     * @return PageInterface
     *
     * @throws NotFoundHttpException
     */
    protected function getOr404BySalt($salt)
    {
        if (!($task = $this->container->get('task.handler')->getSalt($salt))) {
            throw new NotFoundHttpException(sprintf('The Task \'%s\' was not found.',$salt));
        }

        return $task;
    }

}