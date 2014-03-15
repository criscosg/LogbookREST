<?php
namespace LogbookREST\LogBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use LogbookREST\EntryBundle\Entity\Entry;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use LogbookREST\UserBundle\Entity\User;
use JMS\Serializer\Annotation\MaxDepth;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="LogRepository")
 * @ExclusionPolicy("all")
 */

class Log
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @Expose
     * */
    protected $id;

    /**
     * @ORM\Column(name="salt", type="string", length=255, nullable=true)
     * @Expose
     */
    protected $salt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="modified", type="datetime", nullable=true)
     */
    protected $modified;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     * @Expose
     */
    protected $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Expose
     */
    protected $description;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="LogbookREST\EntryBundle\Entity\Entry", mappedBy="log", cascade={"persist", "merge", "remove"})
     * @Expose
     * @MaxDepth(0)
     */
    private $entries;

    /**
     * @ORM\ManyToOne(targetEntity="LogbookREST\UserBundle\Entity\ApiUser", inversedBy="logs")
     * @Expose
     */
    private $user;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime", nullable=true)
     * @Assert\Date()
     * @Expose
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\Date()
     * @Expose
     */
    protected $deleted;

    public function __construct()
    {
        $this->entries = new \Doctrine\Common\Collections\ArrayCollection();
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
        if (isset($this->name)){
            return strval($this->name);
        } else {
            return strval($this->id);
        }

    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
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

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getDeleted()
    {
        return $this->deleted;
    }

    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @ORM\PrePersist
     */
    public function setSalt($salt = null)
    {
        if (!$this->salt) {
            if (!$salt) {
                $salt = md5(time());
            }
            $this->salt = $salt;
        }
    }
}