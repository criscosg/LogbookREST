<?php
namespace EasyScrumREST\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectRestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$slugCompanyTransformer = new \SlugEntityTransformer($options["em"], "EasyScrumREST\\UserBundle\\Entity\\Company", 'UserBundle:Company', 'Company');
    	$slugApiUserTransformer = new \SlugEntityTransformer($options["em"], "EasyScrumREST\\UserBundle\\Entity\\ApiUser", 'UserBundle:ApiUser', 'ApiUser');
    	
        $builder->add('title', 'text', array('required' => false))
                ->add('description', 'textarea', array('required' => false))
        		->add('owner', 'entity', array('class'=>'UserBundle:ApiUser', 'required'=>false))->addModelTransformer($slugApiUserTransformer)
        		->add('company', 'entity', array('class'=>'UserBundle:Company', 'required'=>false))->addModelTransformer($slugCompanyTransformer);
    }

    public function getDefaultOptions(array $options)
    {
        return array(
                'data_class' => 'EasyScrumREST\ProjectBundle\Entity\Project',
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'EasyScrumREST\ProjectBundle\Entity\Project'
        ));
    }

    public function getName()
    {
        return 'project';
    }

}

