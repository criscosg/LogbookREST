<?php
namespace LogbookREST\EntryBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array('required'=>true))
                ->add('text', 'text', array('required'=>true))
                ->add('log', 'entity', array('class'=>'LogBundle:Log', 'required' => false));;
    }

    public function getName()
    {
        return 'entry';
    }

}