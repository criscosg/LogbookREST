<?php

namespace LogbookREST\ImageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use LogbookREST\ImageBundle\Util\FileHelper;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use LogbookREST\EntryBundle\Entity\Entry;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="ImageEntryRepository")
 * @ExclusionPolicy("all")
 */
class ImageEntry extends Image
{
    protected $subdirectory = "images/entry";
    protected $maxImages = 100;

    /**
     * @var Entry $entry
     *
     * @ORM\ManyToOne(targetEntity="LogbookREST\EntryBundle\Entity\Entry", inversedBy="images")
     */
    protected $entry;

    public function getEntry()
    {
        return $this->entry;
    }

    public function setEntry(Entry $entry)
    {
        $this->entry = $entry;
    }

    public function createCopies()
    {
        list($oldRoute, $copies) = parent::createCopies();
        if ($nav = $this->createNav()) {
            $copies[] = $nav;
        }

        return array($oldRoute, $copies);
    }
}
