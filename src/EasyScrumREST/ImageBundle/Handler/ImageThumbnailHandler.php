<?php

namespace EasyScrumREST\ImageBundle\Handler;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\BrowserKit\Request;
use EasyScrumREST\ImageBundle\Form\Type\ImageType;
use EasyScrumREST\ImageBundle\Entity\ImageHorse;

class ImageThumbnailHandler
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function get($id)
    {
        return $this->em->getRepository('ImageBundle:ImageCopy')->find($id);
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
            $thumb=$image->getImageThumbnail();
            if ($thumb) {
                $images[]=$thumb->getWebFilePath();
            }
        }
        
        return $images;
    }
}