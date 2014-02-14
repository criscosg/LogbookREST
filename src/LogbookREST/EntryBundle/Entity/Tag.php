<?php
namespace LogbookREST\EntryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use LogbookREST\EntryBundle\Entity\Horse;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity()
 * @ExclusionPolicy("all")
 */

class Tag
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @Expose
     * */
    protected $id;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="modified", type="datetime", nullable=true)
     */
    protected $modified;

    /**
     * @ORM\Column(type="text")
     * @Expose
     */
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="LogbookREST\EntryBundle\Entity\Entry", inversedBy="tags")
     */
    private $entries;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime", nullable=true)
     * @Assert\Date()
     * @Expose
     */
    protected $created;

    public function __construct()
    {
        $this->entries= new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getModified()
    {
        return $this->modified;
    }

    public function setModified($modified)
    {
        $this->modified = $modified;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function setCreated($created)
    {
        $this->created = $created;
    }

    public function __toString()
    {
        return $this->text;
    }
    
    public function getEntries()
    {
        return $this->entries;
    }
    
    public function setEntries(ArrayCollection $entries)
    {
        $this->entries = $entries;
    }
    
    public function addEntry(Entry $entry)
    {
        $this->entries->add($entry);
    }
}
