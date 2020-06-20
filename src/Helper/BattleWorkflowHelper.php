<?php

namespace App\Helper;

use App\Entity\Battle;
use Symfony\Component\Workflow\Registry;

class BattleWorkflowHelper
{
    /** @var Registry */
    private Registry $workflows;
    
    public function __construct(Registry $workflows)
    {
        $this->workflows = $workflows;
    }
    
    public function apply(Battle $battle, $transition)
    {
        $workflow = $this->workflows->get($battle);
        $workflow->apply($battle, $transition);
    }
    
    public function can(Battle $battle, $transition)
    {
        $workflow = $this->workflows->get($battle);
        
        return $workflow->can($battle, $transition);
    }
}
