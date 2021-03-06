<?php

namespace App\Form\Type;

use App\Entity\Battle;
use App\Entity\Shot;
use App\Helper\ShotHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShotType extends AbstractType implements EventSubscriberInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('x', IntegerType::class);
        $builder->add('y');
        
        $builder->addEventSubscriber($this);
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Shot::class, 'csrf_protection' => false,]);
        $resolver->setRequired(['battle', 'playerId']);
        $resolver->setAllowedTypes('battle', Battle::class);
        $resolver->setAllowedTypes('playerId', 'string');
    }
    
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'setRandomShot',
            FormEvents::POST_SUBMIT => 'checkValid'
        ];
    }
    
    public function setRandomShot(FormEvent $event)
    {
        /** @var Battle $battle */
        $battle   = $event->getForm()->getConfig()->getOption('battle');
        $playerId = $event->getForm()->getConfig()->getOption('playerId');
        
        $player = $battle->getPlayer($playerId);
        
        // if current player is a computer, then get a calculated shot
        if ($player->getType() === \App\Enum\PlayerType::COMPUTER) {
            $bestShot = ShotHelper::getBestShot($battle, $player);
    
            /** @var Shot $shot */
            $shot = $event->getData();
            $shot->setX($bestShot->getX())->setY($bestShot->getY());
        }
    }
    
    public function checkValid(FormEvent $event)
    {
        /** @var Battle $battle */
        $battle = $event->getForm()->getConfig()->getOption('battle');
        
        /** @var Shot $shot */
        $shot = $event->getData();
        
        if ($shot->getX() > $battle->getOptions()->getWidth()) {
            $event->getForm()->get('x')->addError(new FormError('Value is out of range.'));
        }
        
        if ($shot->getYAscii() > $battle->getOptions()->getHeight()) {
            $event->getForm()->get('y')->addError(new FormError('Value is out of range.'));
        }
    }
}
