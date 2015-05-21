<?php
namespace EasyScrumREST\ProjectBundle\Form;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use EasyScrumREST\UserBundle\Form\AdminUserType;

class ProjectWithOwnerType extends ProjectType
{
    protected $company;
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $company=$this->company;
        parent::buildForm($builder, $options);
        $builder->add('owner', 'entity', array('class'=>'UserBundle:ApiUser',
                                                'query_builder' => function (EntityRepository $er) use($company) {
                                                    return $er->createQueryBuilder('u')
                                                            ->add('select', 'u')
                                                            ->add('from', 'UserBundle:ApiUser u')
                                                            ->add('where', "u.role = 'ROLE_PRODUCT_OWNER' AND u.company=:company")
                                                            ->setParameter('company', $company);
                                                    }, 'required'=>false, 'empty_value'=>'Choose a owner for the product'));
    }

    public function getDefaultOptions(array $options)
    {
        return array(
                'data_class' => 'EasyScrumREST\ProjectBundle\Entity\Project',
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'EasyScrumREST\ProjectBundle\Entity\Project'
        ));
    }

    public function getName()
    {
        return parent::getName();
    }
    
    public function setCompany($company)
    {
        $this->company=$company;
    }

}