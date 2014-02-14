<?php
namespace LogbookREST\UserBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;

use LogbookREST\EntryBundle\Entity\Entry;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use LogbookREST\UserBundle\Entity\User;
use JMS\Serializer\Annotation\Expose;
/**
 * @ORM\Entity()
 */

class ApiUser extends User
{
    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="LogbookREST\EntryBundle\Entity\Entry", mappedBy="user", cascade={"persist", "merge", "remove"})
     * @Expose
     */
    private $entries;
    
    public function __construct()
    {
        $this->entries = new \Doctrine\Common\Collections\ArrayCollection();
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
