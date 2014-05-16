<?php

namespace Esnuab\Libro\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre','text',array(
                'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 2, 'max' => 50))),
                'invalid_message' => 'La dsf está mal'
            ))
            ->add('apellidos', 'text',array(
                'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 2, 'max' => 50))),
                'invalid_message' => 'La dsf está mal'
            ))
            ->add('email', 'text',array(
                'constraints' => array(new Assert\NotBlank(),new Assert\Email()),
                'invalid_message' => 'La dsf está mal'
            ))
            ->add('username', 'text',array(
                'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 2, 'max' => 50))),
                'invalid_message' => 'La dsf está mal'
            ))
            ->add('roles','text',array(
                'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 5, 'max' => 22))),
                'invalid_message' => 'La dsf está mal'
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Esnuab\Libro\Model\Entity\User',
            'csrf_protection'   => false
        ));
    }

    public function getName()
    {
        return "user";
    }
}
