<?php namespace ScubaWhere\Entities;

use ScubaWhere\Helper;
use LaravelBook\Ardent\Ardent;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * Class: LogEntry
 *
 * @see Ardent
 *
 * Used to store an entry associated with a log, each entry corresponds to a diffrent issue
 *
 */
class LogEntry extends Ardent 
{
    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

    protected $fillable = array('log_id', 'description');

    public static $rules = array(
        'description'      => 'required',
        'log_id'           => 'integer'
    );

    public function beforeSave()
    {
        if(isset($this->name))
            $this->name = Helper::sanitiseString($this->name);
        if(isset($this->target))
            $this->target = Helper::sanitiseString($this->target);
    }

    public function company()
    {
       return $this->belongsTo('Company');
    } 

    public function log()
    {
        return $this->belongsTo('Log');
    }
}
