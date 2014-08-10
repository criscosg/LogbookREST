<?php
namespace EasyScrumREST\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class IssueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text', array('required' => true))
                ->add('description', 'textarea', array('required' => false))
                ->add('priority', 'number', array('required' => false));
    }
    
    public function getDefaultOptions(array $options)
    {
        return array(
                'data_class' => 'EasyScrumREST\ProjectBundle\Entity\Issue',
        );
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'EasyScrumREST\ProjectBundle\Entity\Issue'
        ));
    }

    public function getName()
    {
        return 'issue';
    }

}

