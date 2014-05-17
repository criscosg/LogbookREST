<?php
namespace EasyScrumREST\TaskBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text', array('required'=>true))
                ->add('description', 'text', array('required'=>false))
                ->add('hours', 'integer', array('required'=>false))
                ->add('priority', 'text', array('required'=>false))
                ->add('sprint', 'entity', array('class'=>'SprintBundle:Sprint', 'required' => false));;
    }

    public function getName()
    {
        return 'task';
    }

}