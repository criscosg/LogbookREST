<?php
namespace EasyScrumREST\ProjectBundle\Form;

use EasyScrumREST\FrontendBundle\DataTransformer\SaltEntityTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectRestType extends AbstractType
{
    private $em;
    
    public function __construct($em=null)
    {
        $this->em = $em;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$saltApiUserTransformer = new SaltEntityTransformer($this->em, "EasyScrumREST\\ProjectBundle\\Entity\\Project", 'UserBundle:ApiUser', 'Owner');
    	
        $builder->add('title', 'text', array('required' => true))
                ->add('salt', 'text', array('required' => false))
                ->add('description', 'textarea', array('required' => true));
        $builder->add(
                $builder->create('owner', 'text')
                ->addModelTransformer($saltApiUserTransformer)
        );
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

