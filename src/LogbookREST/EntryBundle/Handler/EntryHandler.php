<?php

namespace LogbookREST\EntryBundle\Handler;
use Doctrine\ORM\EntityManager;
use LogbookREST\EntryBundle\Entity\Entry;
use Symfony\Component\Form\FormFactoryInterface;
use LogbookREST\EntryBundle\Form\EntryType;
use Symfony\Component\BrowserKit\Request;

class EntryHandler
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
        return $this->em->getRepository('EntryBundle:Entry')->find($id);
    }

    /**
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 20, $offset = 0, $orderby = null)
    {
        return $this->em->getRepository('EntryBundle:Entry')->findBy(array(), $orderby, $limit, $offset);
    }

    /**
     * Create a new Entry.
     *
     * @param $request
     *
     * @return Entry
     */
    public function post($request)
    {
        $entry = new Entry();

        return $this->processForm($entry, $request, 'POST');
    }
    
    /**
     * @param Entry $entry
     * @param $request
     *
     * @return Entry
     */
    public function put(Entry $entry, $request)
    {
        return $this->processForm($entry, $request);
    }
    
    /**
     * @param Entry $entry
     * @param $request
     *
     * @return Entry
     */
    public function patch(Entry $entry, $request)
    {
        return $this->processForm($entry, $request, 'PATCH');
    }
    
    /**
     * @param Entry $entry
     *
     * @return Entry
     */
    public function delete(Entry $entry)
    {
        $this->em->remove($entry);
        $this->em->flush($entry);
    }
    
    /**
     * Processes the form.
     *
     * @param Entry     $entry
     * @param array         $parameters
     * @param String        $method
     *
     * @return Entry
     *
     * @throws \Exception
     */
    private function processForm(Entry $entry, $request, $method = "PUT")
    {
        $form = $this->factory->create(new EntryType(), $entry, array('method' => $method));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $entry = $form->getData();
            $this->em->persist($entry);
            $this->em->flush($entry);

            return $entry;
        }

        throw new \Exception('Invalid submitted data');
    }
}