<?php

namespace Saxulum\Tests\Crud\Data\Form;

use Saxulum\Tests\Crud\Data\Model\Sample;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SampleEditType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
        ;

        $builder->add('submit', 'Symfony\Component\Form\Extension\Core\Type\SubmitType');
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'data_class' => Sample::classname,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sample_edit';
    }
}
