<?php
namespace EasyScrumREST\SprintBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SprintSearchGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setMethod('GET');
        $builder->add('from', 'date', array('widget' => 'single_text', 'input'  => 'datetime', 'required' => false, 'format' => 'dd/MM/yyyy'))
                ->add('to', 'date', array('widget' => 'single_text', 'input'  => 'datetime', 'required' => false, 'format' => 'dd/MM/yyyy'));
    }

    public function getName()
    {
        return 'sprint_group_search';
    }

}