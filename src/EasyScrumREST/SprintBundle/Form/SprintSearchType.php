<?php
namespace EasyScrumREST\SprintBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SprintSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setMethod('GET');
        $builder->add('name', 'text', array('required' => false))
                ->add('project', 'entity', array('class'=>'ProjectBundle:Project', 'required'=>false, 'empty_value'=>"Choose a project" ))
                ->add('active', 'checkbox', array('required' => false, 'label' => 'Active sprints'));
    }

    public function getName()
    {
        return 'sprint_search';
    }

}