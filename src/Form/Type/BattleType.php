<?php

namespace App\Form\Type;

use App\Entity\Battle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BattleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('players', CollectionType::class, ['entry_type' => PlayerType::class, 'allow_add' => true, 'allow_extra_fields' => true]);
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Battle::class, 'csrf_protection' => false,]);
    }
}
