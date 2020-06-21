<?php

namespace App\Manager;

use App\Entity\Battle;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Workflow\Registry;
use SymfonyBundles\RedisBundle\Redis\ClientInterface;

class BattleManager
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
}
