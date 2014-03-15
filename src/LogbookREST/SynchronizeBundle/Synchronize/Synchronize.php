<?php
namespace LogbookREST\SynchronizeBundle\Synchronize;

use Doctrine\ORM\EntityManager;
use Symfony\Component\BrowserKit\Request;
use LogbookREST\OwnerBundle\Entity\Owner;
use LogbookREST\UserBundle\Entity\ApiUser;
use LogbookREST\EntryBundle\Entity\Entry;
use LogbookREST\SynchronizeBundle\Util\ArrayHelper;

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
                $ownerDB->setClinic($user->getClinic());
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

        return $this->em->getRepository('OwnerBundle:Owner')->findNotInEntities($entities, $user->getClinic()->getId());
    }

    private function compareEntries($entries, ApiUser $user)
    {
        $entities = array();
        foreach ($entries as $entryMobile) {
            $entryDB=$this->em->getRepository('EntryBundle:Entry')->findOneBySalt($entryMobile['salt']);
            if (!$entryDB) {
                $entryDB = new Entry();
                $this->saveEntry($entryMobile, $entryDB);
                $entities[] = $entryDB->getId();
            } else {
                $date=new \DateTime($entryMobile['modified']);
                if($entryDB->getModified() < $date) {
                    $this->saveEntry($entryMobile, $entryDB);
                    $entities[] = $entryDB->getId();
                } elseif ($entryDB->getModified() == $date) {
                    $entities[] = $entryDB->getId();
                }
            }
        }

        return $this->em->getRepository('EntryBundle:Entry')->findNotInEntities($entities, $user->getId());
    }
    
    private function compareImages($images, ApiUser $user)
    {
        $entities = array();
        $uploads = 0;
        foreach ($images as $imageMobile) {
            $imageDB=$this->em->getRepository('ImageBundle:ImageEntry')->findOneBySalt($imageMobile['salt']);
            if (!$imageDB)
                $uploads ++;
            else 
                $entities[]=$imageDB->getId();
        }
        $downloads = $this->em->getRepository('ImageBundle:ImageEntry')->findCountNotInEntities($entities, $user->getClinic()->getId());

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
    
    private function saveEntry($entryMobile, $entryDB)
    {
        foreach ($entryMobile as $property => $value) {
            if ($property=='owner_salt') {
                $entryDB->setOwner($this->em->getRepository('OwnerBundle:Owner')->findOneBySalt($value));
            } elseif($property != 'modified' && $property != 'created' && $property != 'birthdate' && $property != 'deleted') {
                $method = sprintf('set%s', ucwords($property));
                if (method_exists($entryDB, $method)) {
                    $entryDB->$method($value);
                }
            } else {
                if ($value) {
                    $date=new \DateTime($value);
                    $method = sprintf('set%s', ucwords($property));
                    if (method_exists($entryDB, $method)) {
                        $entryDB->$method($date);
                    }
                }
            }
        }
        $this->em->persist($entryDB);
        $this->em->flush();
    }

}
