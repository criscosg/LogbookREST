<?php

namespace EasyScrumREST\ImageBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use EasyScrumREST\ImageBundle\Util\FileHelper;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * @ORM\Table()
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"profile"="ImageProfile"})
 * @ExclusionPolicy("all")
 */
abstract class Image
{
    const WEB_PATH = 'bundles/frontend/img/';
    const UPLOAD_PATH = '/var/www/easyscrumrest/web/uploads/';
    const ERROR_MESSAGE = "Ha ocurrido un error. Asegúrate de subir imágenes JPG o PNG y con menos de 2 megas.";
    const INFO_MESSAGE = "El formato de las imágenes ha de ser JPG o PNG y deben pesar menos de 2 megas.";
    const AJAX_LOADER = 'bundles/frontend/img/ajax-loader.gif';
    const UPLOAD_DIR = 'uploads';

    /**
     * @Expose
     */
    protected $subdirectory = "images";
    /**
     * @Assert\File(maxSize = "2M", mimeTypes = {"image/png", "image/jpg", "image/jpeg"})
     */
    protected $file;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Expose
     */
    protected $id;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="date", nullable=true)
     * @Assert\Date()
     */
    protected $createDate;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated", type="date", nullable=true)
     * @Assert\Date()
     */
    protected $updateDate;

    /**
     * @ORM\Column(name="dateRemove", type="date", nullable=true)
     */
    protected $deletedDate;

    /**
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     * @Expose
     */
    protected $image;

    /**
     * @ORM\Column(name="description", type="string", length=300, nullable=true)
     */
    protected $description;

    protected $maxWidth = 1024;

    protected $maxHeight = 768;

    protected $quality = 70;

    protected $type;

    public function getId()
    {
        return $this->id;
    }

    public function getCreateDate()
    {
        return $this->createDate;
    }

    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setDeletedDate($dateRemove)
    {
        $this->deletedDate = $dateRemove;
    }

    public function getDeletedDate()
    {
        return $this->deletedDate;
    }

    /**
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file)
    {
        $this->file = $file;
    }

    public function setMaxWidth($width)
    {
        $this->maxWidth = $width;
    }

    public function getMaxWidth()
    {
        return $this->maxWidth;
    }

    public function setMaxHeight($height)
    {
        $this->height = $height;
    }

    public function getMaxHeight()
    {
        return $this->maxHeight;
    }

    public function getSubdirectory()
    {
        return $this->subdirectory . '/'
                . FileHelper::getDirectoryNameFromId($this->id);
    }

    public function getWebFilePath()
    {
        return self::UPLOAD_DIR . '/' . $this->getSubdirectory() . '/'
                . $this->image;
    }

    public function uploadImage()
    {
        $nameImage = FileHelper::uploadAndReplaceFile(null, $this->file,
                $this->subdirectory, $this->id);
        $this->image = $nameImage;
        $this->saveResizedImage();
    }

    public function saveResizedImage()
    {
        $originalFilePath = self::UPLOAD_PATH . $this->getSubdirectory() . '/'
                . $this->image;
        $dimensions = $this->getImageDimensions($originalFilePath);
        $this->setMaxWidth($dimensions[0]);
        $this->setMaxHeight($dimensions[1]);
        FileHelper::executeConvert($originalFilePath, $originalFilePath,
                $dimensions[0], $dimensions[1], $this->quality);
    }

    protected function getImageDimensions($originalFilePath = null)
    {
        return array($this->maxWidth, $this->maxHeight);
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreateDate()
    {
        $this->createDate = new \DateTime('today');
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdateDate()
    {
        $this->updateDate = new \DateTime('today');
    }

    public function getSubclase()
    {
        return $this->subclase;
    }

    public function setSubclase($subclase)
    {
        $this->subclase = $subclase;
    }

}
