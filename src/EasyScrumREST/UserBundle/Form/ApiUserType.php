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
        $builder->add('company', 'entity', array('class'=>'UserBundle:Company', 'required'=>false));
    }

    public function getName()
    {
        return 'api_user';
    }

}