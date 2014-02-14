<?php

namespace LogbookREST\ImageBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', 'file', array('required' => true))
                ->add('horse', 'entity', array('class'=>'EntryBundle:Horse'));
    }

    public function getName()
    {
        return 'image';
    }

}
