<?php
namespace EasyScrumREST\TaskBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SearchTaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('project', 'entity', array('class'=>'ProjectBundle:Project', 'required' => false, 'empty_value'=>"Choose a project"))
                ->add('from', 'date', array('widget' => 'single_text', 'input'  => 'datetime', 'required' => false, 'format' => 'dd/MM/yyyy'))
                ->add('to', 'date', array('widget' => 'single_text', 'input'  => 'datetime', 'required' => false, 'format' => 'dd/MM/yyyy'));
        $builder->setMethod('GET');
    }

    public function getName()
    {
        return 'search_task';
    }

}