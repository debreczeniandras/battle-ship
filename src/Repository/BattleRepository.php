<?php

namespace App\Repository;

use App\Entity\Battle;
use App\Entity\GameOptions;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Workflow\Registry;
use SymfonyBundles\RedisBundle\Redis\ClientInterface;

class BattleRepository
{
    private ClientInterface     $redis;
    private SerializerInterface $serializer;
    private Registry            $workflows;
    
    public function __construct(ClientInterface $redis, SerializerInterface $serializer, Registry $workflows)
    {
        $this->redis      = $redis;
        $this->serializer = $serializer;
        $this->workflows  = $workflows;
    }
    
    public function createNewFromOptions(GameOptions $options): Battle
    {
        $battle = Battle::newInstance();
        $battle->setOptions($options);
        
        $workflow = $this->workflows->get($battle);
        $workflow->apply($battle, 'set_options');
        
        $serialized = $this->serializer->serialize($battle, 'json', ['groups' => 'init']);
        $this->redis->set($battle->getId(), $serialized);
        
        return $battle;
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
    
    public function store(Battle $battle)
    {
//        if ($battle->getPlayers()->count() === 2) {
//            $workflow = $this->workflows->get($battle);
//            $workflow->apply($battle, 'set_players');
//        }
        
        $serialized = $this->serializer->serialize($battle, 'json');
        $this->redis->set($battle->getId(), $serialized);
    }
}
