<?php


namespace Scubawhere\Entities;


class AgentCommissionRule extends Ardent
{
    protected $fillable = array('type', 'item_id', 'commission', 'commission_value');
    
    protected $rules = array(
        'type'             => 'required',
        'item_id'          => 'integer',
        'commission'       => '',
        'commission_value' => ''
    );
    
    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function owner()
    {
        return $this->morphTo();
    }
}