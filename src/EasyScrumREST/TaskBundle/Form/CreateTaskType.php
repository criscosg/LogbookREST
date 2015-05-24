<?php
namespace EasyScrumREST\TaskBundle\Form;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CreateTaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = $builder->getData();
        
        $builder->add('title', 'text', array('required'=>true))
        ->add('description', 'text', array('required'=>false))
        ->add('hours', 'integer', array('required'=>false))
        ->add('priority', 'choice', array('choices'=>array('P2'=>'low priority',
                 'P0'=>'maximum priority'),'empty_value'=>'average priority', 'required'=>false))
        ->add('story', 'entity', array('class'=>'ProjectBundle:Backlog',
                    'query_builder' => function (EntityRepository $er) use ($data) {
                        return $er->createQueryBuilder('t')
                            ->add('select', 't')
                            ->add('from', 'ProjectBundle:Backlog t')
                            ->add('where', "t.project =:project")
                            ->setParameter('project', $data->getSprint()->getProject()->getId());
                    }, 'required'=>false, 'empty_value'=>'User story'))
        ->add('points', 'integer', array('required'=>false));
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