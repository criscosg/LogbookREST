<?php
namespace EasyScrumREST\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array('required' => false))
            ->add('description', 'textarea', array('required' => false));
    }

    public function getName()
    {
        return 'project';
    }

}

