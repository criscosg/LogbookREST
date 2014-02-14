<?php

namespace LogbookREST\ImageBundle\Handler;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\BrowserKit\Request;
use LogbookREST\ImageBundle\Form\Type\ImageType;
use LogbookREST\ImageBundle\Entity\ImageHorse;

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
    public function all($limit = 20, $offset = 0, $orderby = null, $horse)
    {
        $images=array();
        foreach ($this->em->getRepository('ImageBundle:ImageHorse')->findBy(array('horse'=>$horse), $orderby, $limit, $offset) as $image) {
            $thumb=$image->getImageThumbnail();
            if ($thumb) {
                $images[]=$thumb->getWebFilePath();
            }
        }
        
        return $images;
    }
}