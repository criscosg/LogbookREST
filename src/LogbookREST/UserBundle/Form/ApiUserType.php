<?php
namespace LogbookREST\UserBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use LogbookREST\UserBundle\Form\AdminUserType;

class ApiUserType extends AdminUserType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    }

    public function getName()
    {
        return 'api_user';
    }

}