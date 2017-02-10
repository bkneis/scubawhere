<?php

namespace Scubawhere\Entities;

use LaravelBook\Ardent\Ardent;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;

class AgentCommissionRule extends Ardent
{
    protected $fillable = array('owner_type', 'owner_id', 'commission', 'commission_value');
    
    public static $rules = array(
        'owner_type'       => 'required',
        'owner_id'         => 'integer',
        'commission'       => 'numeric|between:0,100',
        'commission_value' => 'integer'
    );
    
    public static function create(array $data)
    {
        $rule = new AgentCommissionRule($data);
        if (!$rule->validate()) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, $rule->errors()->all());
        }
        return $rule;
    }
    
    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function owner()
    {
        return $this->morphTo();
    }
}