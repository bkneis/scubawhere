<?php namespace ScubaWhere\Entities;

use ScubaWhere\Helper;
use LaravelBook\Ardent\Ardent;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * Class: Log
 *
 * @see Ardent
 *
 * Used to store an object containing information of a filed process to alert the User
 *
 */
class Log extends Ardent 
{
    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

    protected $fillable = array('name');

    public static $rules = array(
        'name'      =>  'required'
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

    public function entries()
    {
        return $this->hasMany('ScubaWhere\Entities\LogEntry');
    }
}
