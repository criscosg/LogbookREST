<?php

namespace EasyScrumREST\TaskBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UrgencyHoursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('hoursSpent', 'number', array('required'=>true))
                ->add('hoursEnd', 'number', array('required'=>true));
    }
    
    public function getDefaultOptions(array $options)
    {
        return array(
                'data_class' => 'EasyScrumREST\TaskBundle\Entity\Urgency',
        );
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'EasyScrumREST\TaskBundle\Entity\Urgency'
        ));
    }

    public function getName()
    {
        return 'urgency_hours';
    }

}