<?php
namespace EasyScrumREST\SynchronizeBundle\Synchronize;

use Doctrine\ORM\EntityManager;
use Symfony\Component\BrowserKit\Request;
use EasyScrumREST\VeterinaryBundle\Entity\Veterinary;
use EasyScrumREST\SynchronizeBundle\Util\ArrayHelper;
use EasyScrumREST\ImageBundle\Entity\ImageTask;
use EasyScrumREST\ImageBundle\Util\ImageHelper;

class SynchronizeImages
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function synchronize($mobileDB, Veterinary $veterinary)
    {
        if (isset($mobileDB['images'])) {
            $images=$this->compareImages($mobileDB['images'], $veterinary);
        } else {
            $images=$this->compareImages(array(), $veterinary);
        }

        return array('images'=>$this->processImageArray($images));
    }
    
    private function compareImages($images, Veterinary $veterinary)
    {
        $entities = array();
        $uploads = 0;
        foreach ($images as $imageMobile) {
            $imageDB=$this->em->getRepository('ImageBundle:ImageTask')->findOneBySalt($imageMobile['salt']);
            if (!$imageDB) {
                $imageDB = new ImageTask();
                $imageDB->setSalt($imageMobile['salt']);
                $this->em->persist($imageDB);
                $this->em->flush();
                $this->saveImage($imageMobile, $imageDB);
                $entities[] = $imageDB->getId();
            } else {
                $entities[] = $imageDB->getId();
            }
        }

        return $this->em->getRepository('ImageBundle:ImageTask')->findNotInEntities($entities, $veterinary->getCompany()->getId());
    }
    
    private function saveImage($imageMobile,ImageTask $imageDB)
    {
        foreach ($imageMobile as $property => $value) {
            if ($property=='dental_salt') {
                $imageDB->setDental($this->em->getRepository('ToothBundle:Dental')->findOneBySalt($value));
            } elseif ($property=='image') {
                if (ImageHelper::fromOctetToJpg($value, $imageDB)) {
                    $imageDB->setImage($imageDB->getId().'.jpg');
                    list($oldRoute, $copies) = $imageDB->createCopies();
                    $imageDB->uploadNewCopies($copies);
                    foreach ($copies as $copy){
                        $this->em->persist($copy);
                        $this->em->flush($copy);
                    }
                } else {
                    $this->em->remove($imageDB);
                    $this->em->flush();
                }
            } elseif ($property != 'modified' && $property != 'created') {
                $method = sprintf('set%s', ucwords($property));
                if (method_exists($imageDB, $method)) {
                    $imageDB->$method($value);
                }
            } else {
                if ($value) {
                    $date=new \DateTime($value);
                    $method = sprintf('set%s', ucwords($property));
                    if (method_exists($imageDB, $method)) {
                        $imageDB->$method($date);
                    }
                }
            }
        }
        $this->em->persist($imageDB);
        $this->em->flush();
    }

    private function processImageArray($images)
    {
        $images=ArrayHelper::flattMultilevelEntityArray($images);
        foreach ($images as & $image) {
            $image['image']=ImageHelper::fromImageToBase($image);
            $date=new \DateTime('now');
            $image['synchronized']=$date->format('Y-m-d H:m:s');
        }

        return $images;
    }
}