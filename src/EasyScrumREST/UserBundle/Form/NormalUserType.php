<?php
namespace EasyScrumREST\UserBundle\Form;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use EasyScrumREST\UserBundle\Form\AdminUserType;

class NormalUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', 'email', array('required'=>false))
            ->add('password', 'password', array('required'=>false))
            ->add('name', 'text', array('required'=>false))
            ->add('lastName', 'text', array('required'=>false))
            ->add('role', 'choice', array('choices'=>array('ROLE_TEAM'=>'Team member',
                    'ROLE_PRODUCT_OWNER'=>'Product owner',
                    'ROLE_SCRUM_MASTER'=>'Scrum master'), 'required'=>false));
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