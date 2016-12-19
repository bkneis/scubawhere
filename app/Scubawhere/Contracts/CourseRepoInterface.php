<?php

namespace Scubawhere\Repositories;

interface CourseRepoInterface {
    
    function getUsedInFutureBookings($id, $fail = true);
    
}
