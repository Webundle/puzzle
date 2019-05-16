<?php

namespace Puzzle\ContactBundle\Form\Type;

use Puzzle\ContactBundle\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('firstName', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'contact.property.contact.firstName',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [
                    'class' => 'md-input'
                ],
            ])
            ->add('lastName', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'contact.property.contact.lastName',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [
                    'class' => 'md-input'
                ],
            ])
            ->add('email', EmailType::class, [
                'translation_domain' => 'messages',
                'label' => 'contact.property.contact.email',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [
                    'class' => 'md-input'
                ],
            ])
            ->add('phone', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'contact.property.contact.phone',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [
                    'class' => 'md-input'
                ],
                'required' => false
            ])
            ->add('picture', HiddenType::class, array(
                'translation_domain' => 'messages',
                'label' => 'contact.property.contact.picture',
                'required' => false
            ))
            ->add('company', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'contact.property.contact.company',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [
                    'class' => 'md-input'
                ],
                'required' => false
            ])
            ->add('position', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'contact.property.contact.position',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [
                    'class' => 'md-input'
                ],
                'required' => false
            ])
            ->add('location', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'contact.property.contact.location',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [
                    'class' => 'md-input'
                ],
                'required' => false
            ])
            ->add('save', SubmitType::class, array(
                'translation_domain' => 'messages',
                'label' => 'button.save',
                'attr' => [
                    'class' => "md-fab md-fab-accent"
                ]
            ))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Contact::class,
            'validation_groups' => array(
                Contact::class,
                'determineValidationGroups',
            ),
        ));
    }
}