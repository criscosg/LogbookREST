<?php
namespace EasyScrumREST\UserBundle\Form;

use EasyScrumREST\ImageBundle\Form\Type\ImageProfileType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('profileImage',new ImageProfileType())
            ->add('email', 'email', array('required'=>false))
            ->add('password', 'password', array('required'=>false))
            ->add('name', 'text', array('required'=>false))
            ->add('lastName', 'text', array('required'=>false));
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
        return 'profile';
    }

}