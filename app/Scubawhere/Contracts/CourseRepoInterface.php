<?php

namespace Scubawhere\Repositories;

interface CourseRepoInterface {
    
    function getWithFutureBookings($id, $fail = true);
    
}
