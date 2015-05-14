<?php
namespace EasyScrumREST\UserBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TeamGroupType extends AbstractType
{
    private $company;

    public function __construct($company) {
        $this->company = $company;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $company = $this->company;

        $builder->add('name', 'text', array('required'=>true))
                ->add('users', 'entity', array('class'=>'UserBundle:ApiUser',
                    'required' => false,
                    'empty_value' => 'Select team members',
                    'query_builder' => function (EntityRepository $er) use ($company) {
                    return $er->createQueryBuilder('u')
                        ->add('select', 'u')
                        ->add('from', 'UserBundle:ApiUser u')
                        ->andWhere("u.role = 'ROLE_TEAM'")
                        ->andWhere("u.company =:company")
                        ->setParameter('company', $company);
                    },
                    'multiple' => true, 'expanded' => false));
    }

    public function getDefaultOptions(array $options)
    {
        return array(
                'data_class' => 'EasyScrumREST\UserBundle\Entity\TeamGroup',
        );
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'EasyScrumREST\UserBundle\Entity\TeamGroup'
        ));
    }
    
    public function getName()
    {
        return 'team_group';
    }

}