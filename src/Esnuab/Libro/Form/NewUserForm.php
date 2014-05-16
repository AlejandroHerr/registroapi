<?php

namespace Esnuab\Libro\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class NewUserForm extends UserForm
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder,$options);
        $builder
            ->add('password','text',array(
                'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 2, 'max' => 50))),
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
