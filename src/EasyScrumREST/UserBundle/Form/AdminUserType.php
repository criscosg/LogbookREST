<?php
namespace EasyScrumREST\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdminUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', 'email', array('required'=>false))
            ->add('password', 'password', array('required'=>false))
            ->add('name', 'text', array('required'=>false))
            ->add('last_name', 'text', array('required'=>false));
    }

    public function getName()
    {
        return 'admin_user';
    }

}