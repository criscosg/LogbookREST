<?php

namespace EasyScrumREST\TaskBundle\Handler;

use Doctrine\ORM\EntityManager;
use EasyScrumREST\TaskBundle\Entity\Tag;
use EasyScrumREST\TaskBundle\Form\TagType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\BrowserKit\Request;

class TagHandler
{
    private $em;
    private $factory;

    public function __construct(EntityManager $em, FormFactoryInterface $formFactory)
    {
        $this->em = $em;
        $this->factory = $formFactory;
    }

    public function get($id)
    {
        return $this->em->getRepository('EasyScrumREST:Tag')->find($id);
    }

    /**
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 20, $offset = 0, $orderby = null)
    {
        return $this->em->getRepository('EasyScrumREST:Tag')->findBy(array(), $orderby, $limit, $offset);
    }

    /**
     * Create a new Tag.
     *
     * @param $request
     *
     * @return Tag
     */
    public function post($task, $request)
    {
        $tag = new Tag();
        $tag->addTask($task);
        return $this->processForm($tag, $request, 'POST', $horse);
    }

    /**
     * @param Tag $tag
     * @param $request
     *
     * @return Tag
     */
    public function put($task, Tag $tag, $request)
    {
        return $this->processForm($tag, $request, "PUT", $task);
    }

    /**
     * @param Tag $tag
     * @param $request
     *
     * @return Tag
     */
    public function patch(Tag $tag, $request, $task)
    {
        return $this->processForm($tag, $request, 'PATCH', $task);
    }

    /**
     * @param Tag $tag
     *
     * @return Tag
     */
    public function delete(Tag $tag)
    {
        $this->em->remove($tag);
        $this->em->flush($tag);
    }

    /**
     * Processes the form.
     *
     * @param Tag     $tag
     * @param array         $parameters
     * @param String        $method
     * @param String        $horse
     *
     * @return Tag
     *
     * @throws \Exception
     */
    private function processForm(Tag $tag, $request, $method = "PUT", $task)
    {
        $form = $this->factory->create(new TagType(), $tag, array('method' => $method));
        $form->handleRequest($request);
        if (!$form->getErrors()) {
            $tag = $form->getData();
            $task->addTag($tag);
            $this->em->persist($tag);
            $this->em->persist($task);
            $this->em->flush();

            return $tag;
        }

        throw new \Exception('Invalid submitted data');
    }
}