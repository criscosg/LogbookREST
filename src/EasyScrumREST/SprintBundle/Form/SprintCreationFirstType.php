<?php
namespace EasyScrumREST\SprintBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SprintCreationFirstType extends AbstractType
{
    protected $company;
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $company=$this->company;
        $builder->add('project', 'entity', array('class'=>'ProjectBundle:Project',
                                                'query_builder' => function (EntityRepository $er) use ($company) {
                                                    return $er->createQueryBuilder('p')
                                                            ->add('select', 'p')
                                                            ->add('from', 'ProjectBundle:Project p')
                                                            ->add('where', "p.company =:company")
                                                            ->setParameter('company', $company);
                                                    }, 'required'=>true, 'empty_value'=>'Choose a project'))
            ->add('dateFrom', 'date', array('widget' => 'single_text', 'input'  => 'datetime', 'required' => true, 'format' => 'dd/MM/yyyy'))
            ->add('dateTo', 'date', array('widget' => 'single_text', 'input'  => 'datetime', 'required' => true, 'format' => 'dd/MM/yyyy'))
            ->add('hoursAvailable', 'number', array('required'=>true))
            ->add('focus', 'number', array('required'=>true));
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
        return 'sprint_first';
    }

    public function setCompany($company)
    {
        $this->company=$company;
    }

}