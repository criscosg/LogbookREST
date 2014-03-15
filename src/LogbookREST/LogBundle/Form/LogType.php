<?php
namespace LogbookREST\LogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array('required' => false))
            ->add('description', 'textarea', array('required' => false))
            ->add('user', 'entity', array('class'=>'UserBundle:ApiUser', 'required'=>false));
    }

    public function getName()
    {
        return 'log';
    }

}

