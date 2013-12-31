<?php

namespace Esnuab\Libro\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class SocioForm extends AbstractType {
    
    
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
        	->add('nombre','text',array(
                'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 2, 'max' => 50))),
                'invalid_message' => 'La dsf está mal'
            ))
            ->add('apellido', 'text',array(
                'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 2, 'max' => 50))),
                'invalid_message' => 'La dsf está mal'
            ))
            ->add('email', 'text',array(
                'constraints' => array(new Assert\NotBlank(),new Assert\Email()),
                'invalid_message' => 'La dsf está mal'
            ))
            ->add('esncard', 'text',array(
                'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 10, 'max' => 15))),
                'invalid_message' => 'La dsf está mal'
            ))
            ->add('passport','text',array(
                'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 5, 'max' => 22))),
                'invalid_message' => 'La dsf está mal'
            ))
            ->add('pais','text',array(
                'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 2, 'max' => 2))),
                'invalid_message' => 'La dsf está mal'
            ))
            ->add('created_at','date',array(
                'input' => 'string',
                'widget' => 'single_text',
                'constraints' => array(new Assert\NotBlank())
            ))
        ;    
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)    {
        $resolver->setDefaults(array(
            'data_class' => 'Esnuab\Libro\Model\Entity\Socio',
            'csrf_protection'   => false
        ));
    }
    
    public function getName() {
        return "socio";
    }
}
