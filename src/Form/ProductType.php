<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Stock;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Le nom est requis']),
                ],
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix (€)',
                'currency' => 'EUR',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Le prix est requis']),
                    new Positive(['message' => 'Le prix doit être positif']),
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image du produit',
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/jpg'],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPG, PNG)',
                    ]),
                ],
            ])
            ->add('featured', CheckboxType::class, [
                'label' => 'Mettre en avant sur la page d\'accueil',
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
            ]);

        // Ajouter les champs de stock pour chaque taille
        foreach (Stock::SIZES as $size) {
            $builder->add('stock_' . $size, IntegerType::class, [
                'label' => 'Stock ' . $size,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0,
                ],
                'data' => $options['data']?->getStockForSize($size)?->getQuantity() ?? 0,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}