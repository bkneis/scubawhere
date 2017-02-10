<?php

namespace Scubawhere\Transformers;

use Illuminate\Database\Eloquent\Model;

class AgentTransformer extends Transformer
{
    protected $commissionRulesTransformer;
    
    public function __construct()
    {
        $this->commissionRulesTransformer = new AgentCommissionRuleTransformer();
    }

    /**
     * Transform an entity by removing unwanted data before returned as JSON
     *
     * @param Model $agent
     * @return mixed
     */
    public function transform($agent)
    {
        if ($agent instanceof Model) {
            $agent = $agent->toArray();
        }
        
        return [
            'id'               => $agent['id'],
            'name'             => $agent['name'],
            'terms'            => $agent['terms'],
            'website'          => $agent['website'],
            'billing_address'  => $agent['billing_address'],
            'billing_email'    => $agent['billing_email'],
            'billing_phone'    => $agent['billing_phone'],
            'branch_address'   => $agent['branch_address'],
            'branch_email'     => $agent['branch_email'],
            'branch_name'      => $agent['branch_name'],
            'branch_phone'     => $agent['branch_phone'],
            'commission'       => $agent['commission'],
            'commission_rules' => $this->commissionRulesTransformer->transformMany($agent['commission_rules'])
        ];
    }
}