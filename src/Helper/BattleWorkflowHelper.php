<?php

namespace App\Helper;

use Symfony\Component\Workflow\Registry;

class BattleWorkflowHelper
{
    /** @var Registry */
    private Registry $workflows;
    
    public function __construct(Registry $workflows)
    {
        $this->workflows = $workflows;
    }
    
    public function apply($subject, $transition)
    {
        $workflow = $this->workflows->get($subject);
        $workflow->apply($subject, $transition);
    }
    
    public function can($subject, $transition)
    {
        $workflow = $this->workflows->get($subject);
        
        return $workflow->can($subject, $transition);
    }
}
