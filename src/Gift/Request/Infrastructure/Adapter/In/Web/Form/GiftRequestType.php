<?php

declare(strict_types=1);

namespace App\Gift\Request\Infrastructure\Adapter\In\Web\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * UI Layer - Symfony Form Type.
 *
 * Defines the structure and validation of a web form.
 * Part of the UI layer in hexagonal architecture.
 *
 * Used by controllers to:
 * - Render HTML forms
 * - Validate user input
 * - Map form data to DTOs/Commands
 */
final class GiftRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('requesterName', TextType::class, [
                'label' => 'Your full name',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(message: 'Name is required'),
                    new Assert\Length(min: 2, max: 100),
                ],
                'attr' => [
                    'class' => 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent',
                    'placeholder' => 'John Doe',
                ],
            ])
            ->add('requesterEmail', EmailType::class, [
                'label' => 'Your email',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(message: 'Email is required'),
                    new Assert\Email(message: 'Invalid email'),
                ],
                'attr' => [
                    'class' => 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent',
                    'placeholder' => 'john.doe@example.com',
                ],
            ])
            ->add('requesterPhone', TelType::class, [
                'label' => 'Your phone number',
                'required' => false,
                'constraints' => [
                    new Assert\Length(max: 20),
                ],
                'attr' => [
                    'class' => 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent',
                    'placeholder' => '+1 234 567 8900',
                ],
            ])
            ->add('requestedGift', TextType::class, [
                'label' => 'Requested gift',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(message: 'Requested gift is required'),
                    new Assert\Length(min: 2, max: 255),
                ],
                'attr' => [
                    'class' => 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent',
                    'placeholder' => 'Electric bicycle, Laptop, etc.',
                ],
            ])
            ->add('motivation', TextareaType::class, [
                'label' => 'Why do you want this gift?',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(message: 'Motivation is required'),
                    new Assert\Length(min: 10, max: 2000),
                ],
                'attr' => [
                    'class' => 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent',
                    'rows' => 5,
                    'placeholder' => 'Explain why you would like to receive this gift...',
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Submit my request',
                'attr' => [
                    'class' => 'w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Uncomment to map form to a DTO class:
            // 'data_class' => GiftRequestInput::class,

            'attr' => [
                'class' => 'needs-validation',
                'novalidate' => true,
            ],
        ]);
    }
}
