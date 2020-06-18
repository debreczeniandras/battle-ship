<?php

namespace App\Form\Type;

use App\Entity\Battle;
use App\Entity\Player;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlayerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', ChoiceType::class, ['choices' => ['A', 'B']]);
        $builder->add('type', ChoiceType::class, ['choices' => Player::PLAYERTYPES]);
        $builder->add('grid', GridType::class);
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Player::class, 'csrf_protection' => false,]);
    }
}
