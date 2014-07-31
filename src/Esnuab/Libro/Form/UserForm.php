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
            ->add('username','text',array(
                'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 2, 'max' => 50))),
                'invalid_message' => 'El nombre de usuario està mal'
            ))
            ->add('name','text',array(
                'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 2, 'max' => 50))),
                'invalid_message' => 'El nombre està mal'
            ))
            ->add('surname', 'text',array(
                'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 2, 'max' => 50))),
                'invalid_message' => 'El apellido està mal'
            ))
            ->add('email', 'text',array(
                'constraints' => array(new Assert\NotBlank(),new Assert\Email()),
                'invalid_message' => 'El e-mail está mal'
            ))
            ->add('roles','text',array(
                'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 5, 'max' => 22))),
                'invalid_message' => 'El rol està mal'
            ))
            ->add('password','text',array(
                'constraints' => array(new Assert\NotBlank()),
                'invalid_message' => 'El password està mal'
            ))
            ->add('protected','text',array(
                'constraints' => array(new Assert\Length(array('min' => 1, 'max' => 1)))
            ))
            ->add('active','text',array(
                'constraints' => array(new Assert\Length(array('min' => 1, 'max' => 1)))
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
