<?php

namespace Scubawhere\Transformers;

use Illuminate\Database\Eloquent\Model;

class AgentCommissionRuleTransformer extends Transformer
{

    /**
     * Transform an entity by removing unwanted data before returned as JSON
     *
     * @param Model $rule
     * @return mixed
     */
    public function transform($rule)
    {
        if ($rule instanceof Model) {
            $rule = $rule->toArray();
        }

        return [
            'commission' => $rule['commission'],
            'commission_value' => $rule['commission_value'],
            'owner_id' => $rule['owner_id'],
            'owner_type' => $this->removeNamespace($rule['owner_type'])
        ];
    }

    private function removeNamespace($owner_type)
    {
        return strtolower(substr($owner_type, strrpos($owner_type, '\\') + 1));

    }
}