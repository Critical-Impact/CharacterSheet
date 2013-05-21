<?php

namespace CCS\BaseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('files', 'collection', array(
            'type' => new FileType(),
            'error_bubbling' => true,
            'prototype' => true,
            'allow_add' => true,
            'by_reference' => false,
            'allow_delete' => true))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CCS\BaseBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'ccs_basebundle_usertype';
    }
}
