<?php
namespace EasyScrumREST\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', 'email', array('required'=>false))
            ->add('password', 'repeated', array(
                'type' => 'password',
                'first_options' => array('attr' => array('placeholder' => 'Password')),
                'second_options' => array('attr' => array('placeholder' => 'Confirm password')),
                'error_bubbling' => 'true'))
            ->add('company', new CompanyType());
    }
    
    public function getDefaultOptions(array $options)
    {
        return array(
                'data_class' => 'EasyScrumREST\UserBundle\Entity\ApiUser',
        );
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'EasyScrumREST\UserBundle\Entity\ApiUser'
        ));
    }

    public function getName()
    {
        return 'api_user';
    }

}