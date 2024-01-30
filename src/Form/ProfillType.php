<?php
namespace App\Form;

use App\Entity\Photo;
use App\Entity\Profil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfillType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('cover', FileType::class, [
                'label' => 'Photo de couverture',
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


