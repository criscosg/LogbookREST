<?php
namespace EasyScrumREST\TaskBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use EasyScrumREST\FrontendBundle\DataTransformer\SaltEntityTransformer;

class TaskType extends AbstractType
{
    private $em;

    public function __construct($em = null)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $saltSprintTransformer = new SaltEntityTransformer($this->em, "EasyScrumREST\\TaskBundle\\Entity\\Task", 'SprintBundle:Sprint', 'Sprint');

        $builder->add('title', 'text', array('required'=>true))
                ->add('description', 'text', array('required'=>false))
                ->add('hours', 'integer', array('required'=>false))
                ->add('priority', 'text', array('required'=>false));
        $builder->add($builder->create('sprint', 'text')
                    ->addModelTransformer($saltSprintTransformer)
        );
    }

    public function getDefaultOptions(array $options)
    {
        return array(
                'data_class' => 'EasyScrumREST\TaskBundle\Entity\Task',
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'EasyScrumREST\TaskBundle\Entity\Task'
        ));
    }

    public function getName()
    {
        return 'task';
    }

}