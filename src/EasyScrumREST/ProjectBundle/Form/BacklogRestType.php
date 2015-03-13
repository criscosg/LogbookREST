<?php
namespace EasyScrumREST\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BacklogRestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text', array('required' => true))
                ->add('description', 'textarea', array('required' => false))
                ->add('priority', 'number', array('required' => false))
                ->add('state', 'text', array('required' => false))
                ->add('salt', 'text', array('required' => false));
    }
    
    public function getDefaultOptions(array $options)
    {
        return array(
                'data_class' => 'EasyScrumREST\ProjectBundle\Entity\Backlog',
        );
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'EasyScrumREST\ProjectBundle\Entity\Backlog'
        ));
    }

    public function getName()
    {
        return 'backlog';
    }

}

