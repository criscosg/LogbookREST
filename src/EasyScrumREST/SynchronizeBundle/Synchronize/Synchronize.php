<?php
namespace EasyScrumREST\SynchronizeBundle\Synchronize;

use Doctrine\ORM\EntityManager;
use Symfony\Component\BrowserKit\Request;
use EasyScrumREST\OwnerBundle\Entity\Owner;
use EasyScrumREST\UserBundle\Entity\ApiUser;
use EasyScrumREST\TaskBundle\Entity\Task;
use EasyScrumREST\SynchronizeBundle\Util\ArrayHelper;

class Synchronize
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function synchronize($mobileDB, ApiUser $user)
    {
        if (isset($mobileDB['owners'])) {
            $owners=$this->compareOwners($mobileDB['owners'], $user);
        } else {
            $owners=$this->compareOwners(array(), $user);
        }
        if (isset($mobileDB['entries'])) {
            $entries=$this->compareEntries($mobileDB['entries'], $user);
        } else {
            $entries=$this->compareEntries(array(), $user);
        }
        if (isset($mobileDB['images'])) {
            $images=$this->compareImages($mobileDB['images'], $user);
        } else {
            $images=$this->compareImages(array(), $user);
        }

        return array('owners'=>$owners, 'entries'=>ArrayHelper::flattMultilevelEntityArray($entries), 'dentals'=>ArrayHelper::flattMultilevelEntityArray($dental), 'teeth'=>ArrayHelper::flattMultilevelEntityArray($teeth), 'logs'=>ArrayHelper::flattMultilevelEntityArray($logs), 'images'=>$images);
    }

    private function compareOwners($owners, ApiUser $user)
    {
        $entities = array();
        foreach ($owners as $ownerMobile) {
            $ownerDB=$this->em->getRepository('OwnerBundle:Owner')->findOneBySalt($ownerMobile['salt']);
            if (!$ownerDB) {
                $ownerDB = new Owner();
                $ownerDB->setCompany($user->getCompany());
                $this->saveOwner($ownerMobile, $ownerDB);
                $entities[] = $ownerDB->getId();
            } else {
                $date=new \DateTime($ownerMobile['modified']);
                if($ownerDB->getModified() < $date) {
                    $this->saveOwner($ownerMobile, $ownerDB);
                    $entities[] = $ownerDB->getId();
                } elseif($ownerDB->getModified() == $date) {
                    $entities[] = $ownerDB->getId();
                }
            }
        }

        return $this->em->getRepository('OwnerBundle:Owner')->findNotInEntities($entities, $user->getCompany()->getId());
    }

    private function compareEntries($entries, ApiUser $user)
    {
        $entities = array();
        foreach ($entries as $taskMobile) {
            $taskDB=$this->em->getRepository('EasyScrumREST:Task')->findOneBySalt($taskMobile['salt']);
            if (!$taskDB) {
                $taskDB = new Task();
                $this->saveTask($taskMobile, $taskDB);
                $entities[] = $taskDB->getId();
            } else {
                $date=new \DateTime($taskMobile['modified']);
                if($taskDB->getModified() < $date) {
                    $this->saveTask($taskMobile, $taskDB);
                    $entities[] = $taskDB->getId();
                } elseif ($taskDB->getModified() == $date) {
                    $entities[] = $taskDB->getId();
                }
            }
        }

        return $this->em->getRepository('EasyScrumREST:Task')->findNotInEntities($entities, $user->getId());
    }
    
    private function compareImages($images, ApiUser $user)
    {
        $entities = array();
        $uploads = 0;
        foreach ($images as $imageMobile) {
            $imageDB=$this->em->getRepository('ImageBundle:ImageTask')->findOneBySalt($imageMobile['salt']);
            if (!$imageDB)
                $uploads ++;
            else 
                $entities[]=$imageDB->getId();
        }
        $downloads = $this->em->getRepository('ImageBundle:ImageTask')->findCountNotInEntities($entities, $user->getCompany()->getId());

        return array('uploads'=>strval($uploads), 'downloads'=>$downloads);
    }
    
    private function saveOwner($ownerMobile, $ownerDB)
    {
        foreach ($ownerMobile as $property => $value) {
            if($property != 'modified' && $property != 'created' && $property != 'deleted') {
                $method = sprintf('set%s', ucwords($property));
                if (method_exists($ownerDB, $method)) {
                    $ownerDB->$method($value);
                }
            }  else {
                if ($value) {
                    $date=new \DateTime($value);
                    $method = sprintf('set%s', ucwords($property));
                    if (method_exists($ownerDB, $method)) {
                        $ownerDB->$method($date);
                    }
                }
            }
        }
        $this->em->persist($ownerDB);
        $this->em->flush();
    }
    
    private function saveTask($taskMobile, $taskDB)
    {
        foreach ($taskMobile as $property => $value) {
            if ($property=='owner_salt') {
                $taskDB->setOwner($this->em->getRepository('OwnerBundle:Owner')->findOneBySalt($value));
            } elseif($property != 'modified' && $property != 'created' && $property != 'birthdate' && $property != 'deleted') {
                $method = sprintf('set%s', ucwords($property));
                if (method_exists($taskDB, $method)) {
                    $taskDB->$method($value);
                }
            } else {
                if ($value) {
                    $date=new \DateTime($value);
                    $method = sprintf('set%s', ucwords($property));
                    if (method_exists($taskDB, $method)) {
                        $taskDB->$method($date);
                    }
                }
            }
        }
        $this->em->persist($taskDB);
        $this->em->flush();
    }

}
