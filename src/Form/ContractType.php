<?php

namespace App\Form;

use App\Entity\Contract;
use App\Entity\Crypto;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContractType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('crypto',EntityType::class, ['label' => 'Crypto', 'class' => Crypto::class, 'choice_label' => 'name', 'multiple' => false, 'expanded' => false])
            ->add('price')
            ->add('quantity')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contract::class,
        ]);
    }
}
