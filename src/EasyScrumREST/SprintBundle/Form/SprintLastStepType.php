<?php
namespace EasyScrumREST\SprintBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SprintLastStepType extends AbstractType
{
    protected $company;

    public function __construct($company) {
        $this->company = $company;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $company=$this->company;
        $builder->add('title', 'text', array('required' => true))
                ->add('description', 'textarea', array('required' => false))
                ->add('teamGroup', 'entity', array('class'=>'UserBundle:TeamGroup',
                    'query_builder' => function (EntityRepository $er) use ($company) {
                        return $er->createQueryBuilder('t')
                            ->add('select', 't')
                            ->add('from', 'UserBundle:TeamGroup t')
                            ->add('where', "t.company =:company")
                            ->setParameter('company', $company);
                    }, 'required'=>true, 'empty_value'=>'Visible by all team members'));
    }

    public function getName()
    {
        return 'sprint';
    }

}