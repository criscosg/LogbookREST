<?php
namespace EasyScrumREST\ImageBundle\Entity;
use Doctrine\Tests\DBAL\Types\IntegerTest;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class ImageThumbnail extends ImageCopy
{
    protected $maxWidth = 120;
    protected $maxHeight = 120;
    protected $sufix = "thumb";
}