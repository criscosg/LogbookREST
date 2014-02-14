<?php
namespace LogbookREST\EntryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use LogbookREST\UserBundle\Entity\ApiUser;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity()
 * @ExclusionPolicy("all")
 */

class Entry
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
     * @ORM\Column(type="string", length=250)
     * @Expose
     */
    protected $name;
    
    /**
     * @ORM\Column(type="text", length=250, nullable=true)
     * @Expose
     */
    protected $text;

    /**
     * @ORM\ManyToOne(targetEntity="LogbookREST\UserBundle\Entity\ApiUser", inversedBy="entries")
     * @Expose
     */
    private $user;
    
    /**
     * @ORM\ManyToMany(targetEntity="LogbookREST\EntryBundle\Entity\Tag", mappedBy="entries")
     * @Expose
     */
    private $tags;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime", nullable=true)
     * @Assert\Date()
     * @Expose
     */
    protected $created;


    public function __construct()
    {
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

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
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
        return $this->name;
    }

    public function getUser()
    {
        return $this->user;
    }
    
    public function setUser(ApiUser $user)
    {
        $this->user = $user;
    }
    
    public function getTags()
    {
        return $this->tags;
    }
    
    public function setTags(ArrayCollection $tags)
    {
        $this->tags = $tags;
    }
    
    public function addTag(Entry $tag)
    {
        $this->tags->add($tag);
    }
}
