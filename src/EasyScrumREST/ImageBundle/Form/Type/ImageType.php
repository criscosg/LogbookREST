<?php

namespace EasyScrumREST\ImageBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', 'file', array('required' => true))
                ->add('task', 'entity', array('class'=>'EasyScrumREST:Task'));
    }

    public function getName()
    {
        return 'image';
    }

}
