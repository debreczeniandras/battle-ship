<?php

namespace App\Manager;

use App\Entity\Battle;
use App\Entity\Player;
use App\Entity\Shot;
use App\Helper\BattleWorkflowHelper;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Workflow\Registry;
use SymfonyBundles\RedisBundle\Redis\ClientInterface;

class BattleManager
{
    private ClientInterface      $redis;
    private SerializerInterface  $serializer;
    private BattleWorkflowHelper $workflow;
    
    public function __construct(ClientInterface $redis, SerializerInterface $serializer, BattleWorkflowHelper $workflow)
    {
        $this->redis      = $redis;
        $this->serializer = $serializer;
        $this->workflow   = $workflow;
    }
    
    public function findById($id, $contextGroups = []): Battle
    {
        $data    = $this->redis->get($id);
        $context = [AbstractObjectNormalizer::SKIP_NULL_VALUES => true];
        
        if ($contextGroups) {
            $context['groups'] = $contextGroups;
        }
        
        /** @var Battle $battle */
        $battle = $this->serializer->deserialize($data, Battle::class, 'json', $context);
        
        return $battle;
    }
    
    public function store(Battle $battle, $contextGroup)
    {
        $serialized = $this->serializer->serialize($battle, 'json', ['groups' => [$contextGroup]]);
        $this->redis->set($battle->getId(), $serialized);
    }
    
    public function shoot(Battle $battle, string $playerId, Shot $shot)
    {
        // check state of the battle
        if ($battle->getState() !== 'playing') {
            throw new BadRequestHttpException(sprintf('Shooting is not allowed at this state [%s]',
                                                      $battle->getState()));
        }
        
        $player = $battle->getPlayer($playerId);
        
        // check state of the player - which one can shoot
        if (!$this->workflow->can($player, 'shoot')) {
            throw new BadRequestHttpException("It's not your turn.");
        }
        
        // check if this shot has already been fired?
        if ($player->getGrid()->hasShot($shot)) {
            throw new BadRequestHttpException("This shot has already been fired.");
        }
        
        // check if it's a hit against opponents grid
        $opponent = $battle->getOpponent($player);
        if ($opponent->getGrid()->isHit($shot)) {
            $shot->setHit();
        }
        
        // take the shot
        $player->getGrid()->addShot($shot);
        
        // check if a ship has been sunk
        if ($shot->isHit()) {
            if ($this->isShipSunk($battle, $player, $shot)) {
                $shot->setSunk();
            }
        }
        
        // set state of the players
//        $this->workflow->apply($player, 'shoot');
//        $this->workflow->apply($opponent, 'duck');
        
        // persist
        $this->store($battle, 'Default');
        
        return $shot;
    }
    
    private function isShipSunk(Battle $battle, Player $player, Shot $shot)
    {
        $opponent = $battle->getOpponent($player);
        
        // find opponent's ship, we just hit
        $ship = null;
        foreach ($opponent->getGrid()->getShips() as $ship) {
            if (in_array((string)$shot, $ship->getCoordinates())) {
                break;
            }
        }
        
        // intersect all the shots and the ship's coordinates
        $shipHits = array_intersect($ship->getCoordinates(), $player->getGrid()->getShotCoordinates());
        
        return count($shipHits) === $ship->getLength();
    }
}
