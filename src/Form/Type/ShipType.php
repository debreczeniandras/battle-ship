<?php

namespace App\Form\Type;

use App\Entity\Ship;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShipType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', ChoiceType::class, ['choices' => Ship::CHOICES]);
        $builder->add('start', CoordinateType::class);
        $builder->add('end', CoordinateType::class);
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Ship::class, 'csrf_protection' => false,]);
    }
}
