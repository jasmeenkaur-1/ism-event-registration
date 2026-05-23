<?php
namespace App\Form;

use App\Entity\Registration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, ['label' => 'First Name', 'attr' => ['class' => 'form-control']])
            ->add('lastName', TextType::class, ['label' => 'Last Name', 'attr' => ['class' => 'form-control']])
            ->add('email', EmailType::class, ['label' => 'Email', 'attr' => ['class' => 'form-control']])
            ->add('company', TextType::class, ['label' => 'Company', 'required' => false, 'attr' => ['class' => 'form-control']])
            ->add('mealPreference', ChoiceType::class, [
                'label' => 'Meal Preference',
                'choices' => ['Meat' => 'meat', 'Vegetarian' => 'vegetarian', 'Vegan' => 'vegan'],
                'attr' => ['class' => 'form-select']
            ])
            ->add('dietaryNotes', TextareaType::class, ['label' => 'Dietary Notes', 'required' => false, 'attr' => ['class' => 'form-control']])
            ->add('submit', SubmitType::class, ['label' => 'Register Now', 'attr' => ['class' => 'btn btn-ism mt-3']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Registration::class]);
    }
}