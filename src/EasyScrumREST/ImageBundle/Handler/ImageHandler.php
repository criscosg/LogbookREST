<?php

namespace EasyScrumREST\ImageBundle\Handler;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\BrowserKit\Request;
use EasyScrumREST\ImageBundle\Form\Type\ImageType;
use EasyScrumREST\ImageBundle\Entity\Image;
use EasyScrumREST\ImageBundle\Entity\ImageTask;

class ImageHandler
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
        return $this->em->getRepository('ImageBundle:Image')->find($id);
    }

    /**
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 20, $offset = 0, $orderby = null, $task)
    {
        $images=array();
        foreach ($this->em->getRepository('ImageBundle:ImageTask')->findBy(array('task'=>$task), $orderby, $limit, $offset) as $image) {
            $images[]=$image->getWebFilePath();
        }

        return $images;
    }

    /**
     * Create a new Image.
     *
     * @param $request
     *
     * @return Image
     */
    public function post($request)
    {
        $image = new ImageTask();
        $this->em->persist($image);
        $this->em->flush();

        return $this->processForm($image, $request, 'POST');
    }

    /**
     * @param Image $image
     * @param $request
     *
     * @return Image
     */
    public function put(Image $image, $request)
    {
        return $this->processForm($image, $request);
    }

    /**
     * @param Image $image
     * @param $request
     *
     * @return $Image
     */
    public function patch(Image $image, $request)
    {
        return $this->processForm($image, $request, 'PATCH');
    }

    /**
     * @param Image $image
     *
     * @return Image
     */
    public function delete(Image $image)
    {
        $this->em->remove($image);
        $this->em->flush($image);
    }

    /**
     * Processes the form.
     *
     * @param Image     $image
     * @param array         $parameters
     * @param String        $method
     *
     * @return Image
     *
     * @throws \Exception
     */
    private function processForm(Image $image, $request, $method = "PUT")
    {
        $form = $this->factory->create(new ImageType(), $image, array('method' => $method));
        $form->handleRequest($request);
        if (!$form->getErrors()) {
            list($oldRoute, $copies) = $image->createCopies();
            $image->uploadImage();
            $this->em->persist($image);
            $this->em->flush($image);
            $image->uploadNewCopies($copies);
            foreach ($copies as $copy){
                $this->em->persist($copy);
                $this->em->flush($copy);
            }

            return $image;
        }
        $this->em->remove($image);
        $this->em->flush();

        throw new \Exception('Invalid submitted data');
    }
}