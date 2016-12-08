<?php

namespace Scubawhere\Repositories;

use Illuminate\Support\ServiceProvider;

class LanguageRepoServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('Scubawhere\Repositories\LanguageRepoInterface', function($app)
        {
            return new LanguageRepo();
        });
    }
}
