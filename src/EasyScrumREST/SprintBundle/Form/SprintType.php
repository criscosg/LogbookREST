<?php
namespace EasyScrumREST\SprintBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SprintType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text', array('required' => false))
            ->add('description', 'textarea', array('required' => false))
            ->add('project', 'entity', array('class'=>'ProjectBundle:Project', 'required'=>false))
            ->add('dateFrom', 'date', array('widget' => 'single_text', 'input'  => 'datetime', 'required' => true, 'format' => 'dd/MM/yyyy'))
            ->add('dateTo', 'date', array('widget' => 'single_text', 'input'  => 'datetime', 'required' => true, 'format' => 'dd/MM/yyyy'))
            ->add('hoursAvailable', 'number', array('required'=>true))
            ->add('focus', 'number', array('required'=>true));
    }

    public function getName()
    {
        return 'sprint';
    }

}

