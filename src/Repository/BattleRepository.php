<?php

namespace App\Repository;

use App\Entity\Battle;
use App\Entity\GameOptions;
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
    
    public function createNewFromOptions(GameOptions $options)
    {
        $battle = Battle::newInstance();
        $battle->setOptions($options);
        
        $workflow = $this->workflows->get($battle);
        $workflow->apply($battle, 'set_options');
        
        $serialized = $this->serializer->serialize($battle, 'json', ['groups' => 'init']);
        $this->redis->set($battle->getId(), $serialized);
        
        return $battle;
    }
    
    public function findById($id)
    {
        $data   = $this->redis->get($id);
        $battle = $this->serializer->deserialize($data, Battle::class, 'json', ['groups' => 'init']);
        
        return $battle;
    }
}
