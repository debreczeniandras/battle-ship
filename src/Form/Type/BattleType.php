<?php

use App\Entity\Battle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BattleType extends AbstractType
{
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Battle::class, 'csrf_protection' => false,]);
    }
}
