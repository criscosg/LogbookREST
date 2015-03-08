<?php
namespace EasyScrumREST\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('salt', 'hidden', array('required' => false));
        $builder->add('password', 'repeated',
                array('type' => 'password',
                        'invalid_message' => 'The password must be the same',
                        'first_name' => 'Password',
                        'second_name' => 'confirm_password',
                        'error_bubbling' => 'true'));
    }

    public function getName()
    {
        return 'reset_password';
    }
}