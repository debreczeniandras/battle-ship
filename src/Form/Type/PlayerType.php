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
    }
    
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::SUBMIT => [['setRandomBoard'], ['setPlayerState']]
        ];
    }
    
    public function setRandomBoard(FormEvent $event)
    {
        /** @var Player $player */
        $player = $event->getData();
        if ($player->getType() === \App\Enum\PlayerType::COMPUTER) {
            $battle = $event->getForm()->getRoot()->getData();
            $grid   = $this->gridHelper->getRandomGrid($battle);
            
            $player->setGrid($grid);
        }
    }
    
    /**
     * Player A should have the state to shoot,
     * Player B should have the state to wait.
     *
     * @param FormEvent $event
     */
    public function setPlayerState(FormEvent $event)
    {
        /** @var Player $player */
        $player = $event->getData();
        if ($player->getId() === 'B') {
            $player->setState('ducking');
        }
    }
}
