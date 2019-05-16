<?php

namespace Puzzle\ContactBundle\Form\Type;

use Puzzle\ContactBundle\Entity\Group;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class GroupFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('name', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'contact.property.group.name',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [
                    'class' => 'md-input'
                ],
            ])
            ->add('description', TextareaType::class, [
                'translation_domain' => 'messages',
                'label' => 'contact.property.group.description',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [
                    'class' => 'md-input',
                    'rows' => 3,
                    'cols' => 10
                ],
                'required' => false
            ])
            ->add('contacts', EntityType::class, array(
                'translation_domain' => 'messages',
                'label' => 'contact.property.group.contacts',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'class' => 'ContactBundle:Contact',
                'choice_label' => 'fullName',
                'multiple' => true,
                'mapped' => false,
                'required' => false
            ))
            ->add('save', SubmitType::class, array(
                'translation_domain' => 'messages',
                'label' => 'button.label.save',
                'attr' => [
                    'class' => "md-btn md-btn-success"
                ]
            ))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'data_class' => Group::class,
            'validation_groups' => array(
                Group::class,
                'determineValidationGroups',
            ),
        ));
    }
}