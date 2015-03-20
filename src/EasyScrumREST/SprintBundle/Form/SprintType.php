<?php
namespace EasyScrumREST\SprintBundle\Form;

use EasyScrumREST\TaskBundle\Form\TaskType;
use EasyScrumREST\FrontendBundle\DataTransformer\SaltEntityTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SprintType extends AbstractType
{
    private $em;

    public function __construct($em=null)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $saltProjectTransformer = new SaltEntityTransformer($this->em, "EasyScrumREST\\SprintBundle\\Entity\\Sprint", 'ProjectBundle:Project', 'Project');

        $builder->add('title', 'text', array('required' => false))
            ->add('description', 'textarea', array('required' => false))
            ->add('salt', 'text', array('required' => false))
            ->add('dateFrom', 'date', array('widget' => 'single_text', 'input'  => 'datetime', 'required' => true, 'format' => 'dd/MM/yyyy'))
            ->add('dateTo', 'date', array('widget' => 'single_text', 'input'  => 'datetime', 'required' => true, 'format' => 'dd/MM/yyyy'))
            ->add('hoursAvailable', 'number', array('required'=>true))
            ->add('hoursPlanified', 'number', array('required'=>false))
            ->add('focus', 'number', array('required'=>true))
            ->add('finalFocus', 'number', array('required'=>true))
            ->add('tasks', 'collection', array('type' => new TaskType(), 'allow_add' => true));
            $builder->add(
                    $builder->create('project', 'text')
                    ->addModelTransformer($saltProjectTransformer)
            );
    }

    public function getDefaultOptions(array $options)
    {
        return array(
                'data_class' => 'EasyScrumREST\SprintBundle\Entity\Sprint',
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'EasyScrumREST\SprintBundle\Entity\Sprint'
        ));
    }

    public function getName()
    {
        return 'sprint';
    }

}

