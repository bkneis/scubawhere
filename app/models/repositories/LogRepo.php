<?php namespace ScubaWhere\Repositories;

use ScubaWhere\Context;
use ScubaWhere\Exceptions;
use ScubaWhere\Entities\Log;
use ScubaWhere\Entities\LogEntry;

/**
 * Class: LogRepo
 *
 * @see LogRepoInterface
 *
 * A repository to act as a DAO for the LogService, it is responsible for the logs and log_entries table
 *
 */
class LogRepo implements LogRepoInterface
{
    protected $log_model;

    public function __construct()
    {
        $this->log_model = Context::get()->logs();
    }

    public function get($id)
    {
        return $this->log_model->with('entries')->findOrFail($id);
    }

    public function getAll()
    {
        return $this->log_model->with('entries')->orderBy('created_at', 'DESC')->get();
    }

    public function create($data)
    {
        $log = new Log($data);

        if( !$log->validate() )
            throw new InputNotValidException($log->errors()->all());

        return $this->log_model->save($log);
    }

    public function delete($id)
    {
        $log = $this->get($id);
        $log->delete($log);
        //$this->log_model->save(); // do i need this?
    }

    public function update($id, $data)
    {
        $log = $this->get($id); // combine into one line ??
        return $log->update($data);
    }

    public function addEntry($id, $entry)
    {
        $log = $this->get($id);
        $data = array('description' => $entry);
        $entry = new LogEntry($data);
        
        if( !$entry->validate() )
            throw new InputNotValidException($log->errors()->all());

        $entry = $log->entries()->save($entry);
        return $log;
    }

    public function removeEntry($id, $entry_id)
    {
        $log = $this->get($id);
        $log->detach($entry_id); // not sure if this will work
        $entry = $log->entries()->findOrFail($entry_id);
        $entry->delete();
        $this->log_model->save($log);
    }

    public function removeAllEntries($id)
    {
        // todo
    }

}
