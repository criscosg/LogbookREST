<?php

namespace LogbookREST\EntryBundle\Handler;

use Doctrine\ORM\EntityManager;
use LogbookREST\EntryBundle\Entity\Tag;
use LogbookREST\EntryBundle\Form\TagType;
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
        return $this->em->getRepository('EntryBundle:Tag')->find($id);
    }

    /**
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 20, $offset = 0, $orderby = null, $horse)
    {
        return $this->em->getRepository('EntryBundle:Tag')->findBy(array('horse' => $horse), $orderby, $limit, $offset);
    }

    /**
     * Create a new Tag.
     *
     * @param $request
     *
     * @return Tag
     */
    public function post($horse, $request)
    {
        $tag = new Tag();
        $tag->setHorse($horse);
        return $this->processForm($tag, $request, 'POST', $horse);
    }

    /**
     * @param Tag $tag
     * @param $request
     *
     * @return Tag
     */
    public function put($horse, Tag $tag, $request)
    {
        return $this->processForm($tag, $request, "PUT", $horse);
    }

    /**
     * @param Tag $tag
     * @param $request
     *
     * @return Tag
     */
    public function patch(Tag $tag, $request, $horse)
    {
        return $this->processForm($tag, $request, 'PATCH', $horse);
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
    private function processForm(Tag $tag, $request, $method = "PUT", $horse)
    {
        $form = $this->factory->create(new TagType(), $tag, array('method' => $method));
        $form->handleRequest($request);
        if (!$form->getErrors()) {
            $tag = $form->getData();
            $tag->setHorse($horse);
            $this->em->persist($tag);
            $this->em->flush($tag);

            return $tag;
        }

        throw new \Exception('Invalid submitted data');
    }
}