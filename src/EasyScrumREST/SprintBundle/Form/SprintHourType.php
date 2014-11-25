<?php
namespace EasyScrumREST\SprintBundle\Form;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SprintHourType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('date', 'date', array('widget' => 'single_text', 'input'  => 'datetime', 'required' => true, 'format' => 'dd/MM/yyyy'))
                ->add('hours', 'number', array('required'=>true));
    }

    public function getDefaultOptions(array $options)
    {
        return array(
                'data_class' => 'EasyScrumREST\SprintBundle\Entity\HoursSprint',
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'EasyScrumREST\SprintBundle\Entity\HoursSprint'
        ));
    }

    public function getName()
    {
        return 'sprint_hours';
    }
}