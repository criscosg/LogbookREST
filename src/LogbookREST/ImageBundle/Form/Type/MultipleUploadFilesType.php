<?php

namespace LogbookREST\ImageBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MultipleUploadFilesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('images', 'file', array("attr" => array('multiple' => 'multiple')));
    }

    public function getName()
    {
        return 'multipleuploadfiles';
    }
}