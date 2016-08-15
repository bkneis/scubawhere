<?php namespace ScubaWhere\Services;

use ScubaWhere\Repositories\LogRepoInterface;

/**
 * Class: Logger
 *
 * Used to create independant logger objects so that more than 1 issue can be logged
 * within the same service instance. It is only instaniated from LogService->create()
 *
 */
class Logger
{
    protected $log;
    protected $log_repo;
    protected $log_contents;

    public function __construct(LogRepoInterface $log_repo, $name)
    {
        $this->log_repo = $log_repo;
        $data = array('name' => $name);
        $this->log = $this->log_repo->create($data);
    }

    public function append($data)
    {
        $this->log_repo->addEntry($this->log->id, $data);
    }

    public function clear()
    {
        // todo
    }

    public function delete()
    {
        $this->log_repo->delete($this->log->id);
    }
}
