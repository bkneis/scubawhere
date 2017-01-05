<?php

namespace Scubawhere\Entities;

trait Packageable {
    
    public function removeFromPackages()
    {
        $packages = $this->packages();
        if ($packages->exists()) {
            \DB::table('packageables')
                ->where('packageable_type', Accommodation::class)
                ->where('packageable_id', $this->id)
                ->update(array('deleted_at' => \DB::raw('NOW()')));
        }
        return $this;
    }

    public function packages()
    {
        return $this->morphToMany(Package::class, 'packageable')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
