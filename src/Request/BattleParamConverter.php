<?php

namespace App\Request;

use App\Entity\Battle;
use App\Entity\Player;
use App\Repository\BattleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class BattleParamConverter implements ParamConverterInterface
{
    /** @var BattleRepository */
    private BattleRepository $repository;
    
    /** @var SerializerInterface */
    private SerializerInterface $serializer;
    
    public function __construct(BattleRepository $repository, SerializerInterface $serializer)
    {
        $this->repository = $repository;
        $this->serializer = $serializer;
    }
    
    public function apply(Request $request, ParamConverter $configuration)
    {
        $options = $configuration->getOptions();
        $param   = $options['requestParam'] ?? $configuration->getName() ?? 'id';
        
        if (!$request->attributes->has($param)) {
            return false;
        }
        
        $value = $request->attributes->get($param);
        
        if (!$value && $configuration->isOptional()) {
            $request->attributes->set($configuration->getName(), null);
            
            return true;
        }
        
        $battle = $this->repository->findById($value);
        $request->attributes->set($configuration->getName(), $battle);
        
        return true;
    }
    
    public function supports(ParamConverter $configuration)
    {
        if (null === $configuration->getClass()) {
            return false;
        }
        
        return $configuration->getClass() === Battle::class;
    }
}
