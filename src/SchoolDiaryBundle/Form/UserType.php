<?php

namespace SchoolDiaryBundle\Form;


use SchoolDiaryBundle\Entity\SchoolClass;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class)
            ->add('password', RepeatedType::class,
                array(
                    'type' => PasswordType::class,
                    'first_options' => array('label' => 'Password'),
                    'second_options' => array('label' => 'Repeat Password'),
                    'invalid_message' => 'The passwords should match!'
                ))
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('personalID', TextType::class, array('label' => 'Personal ID'))
            ->add('studentClass', EntityType::class, array(
                'label' => 'Please select your grade',
                'class' => SchoolClass::class,
                'choice_label' => 'gradeForSelect',
                'required' => false,
                'placeholder' => false
            ))
            ->add('registerTeacher', HiddenType::class, array(
                'required' => false,
                'mapped' => false
            ))
            ->add('image', FileType::class, array(
                'attr' => array('class' => 'custom-file-upload-btn'),
                'label' => 'Select image to upload'
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Register'
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SchoolDiaryBundle\Entity\User'
        ));
    }
}