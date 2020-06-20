<?php

namespace App\Form\Type;

use App\Entity\Battle;
use App\Entity\Player;
use App\Helper\GridHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlayerType extends AbstractType implements EventSubscriberInterface
{
    /** @var GridHelper */
    private GridHelper $gridHelper;
    
    public function __construct(GridHelper $gridHelper)
    {
        $this->gridHelper = $gridHelper;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', ChoiceType::class, ['choices' => ['A', 'B']]);
        $builder->add('type', ChoiceType::class, ['choices' => Player::PLAYERTYPES]);
        $builder->add('grid', GridType::class);
        
        $builder->addEventSubscriber($this);
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Player::class, 'csrf_protection' => false,]);
        $resolver->setRequired('battle');
        $resolver->setAllowedTypes('battle', Battle::class);
    }
    
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData',
        ];
    }
    
    public function onPreSetData(FormEvent $event)
    {
        /** @var Player $player */
        $player = $event->getData();
        if ($player->getType() === \App\Enum\PlayerType::COMPUTER) {
            $battle = $event->getForm()->getConfig()->getOption('battle');
            $grid   = $this->gridHelper->getRandomGrid($battle);
            
            $player->setGrid($grid);
        }
    }
}
