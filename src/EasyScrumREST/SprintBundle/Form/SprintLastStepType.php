<?php
namespace EasyScrumREST\SprintBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SprintLastStepType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text', array('required' => false))
                ->add('description', 'textarea', array('required' => false));
    }

    public function getName()
    {
        return 'sprint';
    }

}