<?php

namespace EasyScrumREST\FrontendBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class StatisticSearchType extends AbstractType {

    protected $company;

    public function __construct($company) {
        $this->company = $company;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setMethod('GET');
        $company=$this->company;
        $builder->add('project', 'entity', array('class'=>'ProjectBundle:Project',
            'query_builder' => function (EntityRepository $er) use ($company) {
                return $er->createQueryBuilder('p')
                    ->add('select', 'p')
                    ->add('from', 'ProjectBundle:Project p')
                    ->add('where', "p.company =:company")
                    ->setParameter('company', $company);
            }, 'required'=>false, 'empty_value'=>'Choose a project'))
            ->add('from', 'date', array('widget' => 'single_text', 'input'  => 'datetime', 'required' => false, 'format' => 'dd/MM/yyyy'))
            ->add('to', 'date', array('widget' => 'single_text', 'input'  => 'datetime', 'required' => false, 'format' => 'dd/MM/yyyy'));
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'EasyScrumREST\FrontendBundle\Util\StatisticSearchHelper',
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'EasyScrumREST\FrontendBundle\Util\StatisticSearchHelper'
        ));
    }

    public function getName()
    {
        return 'statistic_search';
    }
}