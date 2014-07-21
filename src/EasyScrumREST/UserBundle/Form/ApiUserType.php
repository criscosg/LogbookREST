<?php
namespace EasyScrumREST\UserBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use EasyScrumREST\UserBundle\Form\AdminUserType;

class ApiUserType extends AdminUserType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('company', 'entity', array('class'=>'UserBundle:Company', 'required'=>false))
                ->add('roles', 'choice', array('choices'=>array('ROLE_TEAM'=>'Team member',
                                                                'ROLE_PRODUCT_OWNER'=>'Product owner',
                                                                'ROLE_SCRUM_MASTER'=>'Scrum master'), 'required'=>false));
    }

    public function getName()
    {
        return 'api_user';
    }

}