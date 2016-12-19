<?php 

namespace Scubawhere\Repositories;

use Scubawhere\Helper;
use Scubawhere\Context;
use Scubawhere\Exceptions;
use Scubawhere\Entities\Course;
use Scubawhere\Exceptions\Http\HttpNotFound;

/**
 * Class CourseRepo acts as a DAO for the accommodation models.
 *
 * It should always return eloquent models and can be thought of as a collection. If at any point in the project, you
 * find yourself accessing the model to retrieve the same data, please add a function to this class and use that.
 *
 * @package Scubawhere\Repositories
 *
 * @see \Scubawhere\Repositories\BaseRepo
 * @see \Scubawhere\Repositories\CourseRepoInterface
 */
class CourseRepo extends EloquentRepo implements CourseRepoInterface {

    /** 
     * Eloquent model that acts as the root model to associate assets to
     *
     * @var \ScubaWhere\Entities\Company
    */ 
    protected $company_model;
    
    public function __construct() {
        $this->company_model = Context::get();
        parent::__construct(Course::class);
    }

    /**
     * Get a course with all of its bookings that are scheduled in the future
     *
     * @param int  $id
     * @param bool $fail
     *
     * @throws HttpNotFound
     *
     * @return mixed
     */
    public function getUsedInFutureBookings($id, $fail = true)
    {
        $course = Course::with([
            'packages',
            'bookingdetails.session' => function($q) {
                $q->where('start', '>=', Helper::localtime());
            },
            'bookingdetails.training_session' => function($q) {
                $q->where('start', '>=', Helper::localtime());
            }])
            ->find($id);

        if(is_null($course) && $fail) {
            throw new HttpNotFound(__CLASS__ . __METHOD__, ['The course could not be found']);
        }

        return $course;
    }

}