<?php

namespace App\Form;

use App\Entity\Contract;
use App\Entity\Crypto;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContractType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('crypto', EntityType::class, ['label' => 'Crypto', 'class' => Crypto::class, 'choice_label' => 'name', 'multiple' => false, 'expanded' => false])
            ->add('price', NumberType::class, ['html5' => true, 'attr' => ['step' => 0.01, 'min' => 0.01]])
            ->add('quantity', NumberType::class, ['html5' => true, 'attr' => ['step' => 0.000001, 'min' => 0.000001]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contract::class,
        ]);
    }
}
