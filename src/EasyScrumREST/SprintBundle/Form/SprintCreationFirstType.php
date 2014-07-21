<?php
namespace EasyScrumREST\SprintBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SprintCreationFirstType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('fromDate', 'date', array('widget' => 'single_text', 'input'  => 'datetime', 'required' => true, 'format' => 'dd/MM/yyyy'))
        ->add('toDate', 'date', array('widget' => 'single_text', 'input'  => 'datetime', 'required' => true, 'format' => 'dd/MM/yyyy'))
        ->add('hoursAvailable', 'number', array('required'=>true))
        ->add('focus', 'number', array('required'=>true));
    }
    
    public function getDefaultOptions(array $options)
    {
        return array(
                'data_class' => 'EasyScrumREST\SprintBundle\Entity\Sprint',
        );
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'EasyScrumREST\SprintBundle\Entity\Sprint'
        ));
    }
    
    public function getName()
    {
        return 'sprint_first';
    }

}