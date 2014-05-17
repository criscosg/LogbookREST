<?php
namespace EasyScrumREST\SprintBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SprintType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array('required' => false))
            ->add('description', 'textarea', array('required' => false))
            ->add('company', 'entity', array('class'=>'UserBundle:Company', 'required'=>false));
    }

    public function getName()
    {
        return 'sprint';
    }

}

