<?php

namespace EasyScrumREST\TaskBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TaskHoursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('hoursSpent', 'number', array('required'=>false, 'error_bubbling'=>true))
                ->add('hoursEnd', 'number', array('required'=>false, 'error_bubbling'=>true));
    }
    
    public function getDefaultOptions(array $options)
    {
        return array(
                'data_class' => 'EasyScrumREST\TaskBundle\Entity\HoursSpent',
        );
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'EasyScrumREST\TaskBundle\Entity\HoursSpent'
        ));
    }

    public function getName()
    {
        return 'hours';
    }

}