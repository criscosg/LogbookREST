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
                ->add('role', 'choice', array('choices'=>array('ROLE_TEAM'=>'Team member',
                                                                'ROLE_PRODUCT_OWNER'=>'Product owner',
                                                                'ROLE_SCRUM_MASTER'=>'Scrum master'), 'multiple'=>false, 'required'=>false));
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