<?php namespace Scubawhere\Services;

use Scubawhere\Repositories\LogRepoInterface;
use Scubawhere\Exceptions\InputNotValidException;

/**
 * Class: LogService
 *
 * An internal service not exposed to the User but used within other services for logging errors
 * that need to be displayed to the user, i.e. complex validation errors
 *
 */
class LogService 
{
    protected $log_repo;

    public function __construct(LogRepoInterface $log_repo)
    {
        $this->log_repo = $log_repo;
    }

    public function get($id)
    {
        if( !$id )
            throw new InputNotValidException('Log ID is not valid');

        return $this->log_repo->get($id);
    }

    public function getAll()
    {
        return $this->log_repo->getAll();
    }

    public function create($name)
    {
        return new Logger($this->log_repo, $name);
    }

    public function delete($id)
    {
        if( !$id )
            throw new InputNotValidException('Log ID is not valid');

        return $this->log_repo->delete($id);
    }

    public function update($id, $data)
    {
        if( !$id )
            throw new InputNotValidException('Log ID could not be found');

        if( !$data )
            throw new InputNotValidException('No data was provided to update the log');

        return $this->log_repo->update($id, $data);
    }

}
