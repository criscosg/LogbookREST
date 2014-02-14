<?php
namespace LogbookREST\ImageBundle\Entity;
use Doctrine\Tests\DBAL\Types\IntegerTest;
use LogbookREST\ImageBundle\Util\FileHelper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class ImageNav extends ImageCopy
{
    protected $maxWidth = 30;
    protected $maxHeight = 30;
    protected $sufix = "nav";
}