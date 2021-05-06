<?php

namespace App\Form;

use App\Entity\Contract;
use App\Entity\Crypto;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SearchType;

class ContractType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('crypto', EntityType::class, 
                ['placeholder' => 'Sélectionner une crypto', 
                    'label' => false, 
                    'class' => Crypto::class, 
                    'choice_label' => 'name', 
                    'multiple' => false, 
                'expanded' => false
            ])
            // ->add('crypto', SearchType::class, ['label' => false]) //'mapped' => false, 
            ->add('price', NumberType::class, 
                ['label' => false, 
                    'html5' => true, 
                    'attr' => [
                        'step' => 0.01, 
                        'min' => 0.01, 
                        'label' => false
                    ]
            ])
            ->add('quantity', NumberType::class, 
                ['label' => false, 
                'html5' => true, 
                'attr' => [
                    'step' => 0.000001, 
                    'min' => 0.000001, 
                    'label' => false
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contract::class,
        ]);
    }
}
