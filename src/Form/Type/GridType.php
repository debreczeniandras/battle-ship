<?php

namespace App\Form\Type;

use App\Entity\Grid;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GridType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('ships', CollectionType::class, ['entry_type' => ShipType::class, 'allow_add' => true, 'by_reference' => true]);
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Grid::class, 'csrf_protection' => false,]);
    }
}
