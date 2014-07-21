<?php
namespace EasyScrumREST\TaskBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CreateUrgencyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text', array('required'=>true))
                ->add('description', 'text', array('required'=>false));
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
        return 'urgency';
    }

}