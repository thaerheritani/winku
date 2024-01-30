<?php
namespace App\Form;

use App\Entity\Photo;
use App\Entity\Profil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Other fields of your Profil entity
            ->add('photo', FileType::class, [
                'label' => 'Photo de profil',
                'required' => false, // Depending on your needs
                'mapped' => false, // Set to false to prevent trying to map to entity directly
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Adjust 'data_class' to match your Profil entity class
            'data_class' => Profil::class,
        ]);
    }
}

