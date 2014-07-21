<?php
namespace EasyScrumREST\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array('required'=>true));
    }

    public function getDefaultOptions(array $options)
    {
        return array(
                'data_class' => 'EasyScrumREST\UserBundle\Entity\Company',
        );
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'EasyScrumREST\UserBundle\Entity\Company'
        ));
    }
    
    public function getName()
    {
        return 'company';
    }

}