<?php

namespace App\Providers;

use App\Services\VKService;
use Illuminate\Support\ServiceProvider;

class VkServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
	    $this->app->singleton('service.vkconfig', function($app){
		    return new VKService();
	    });
    }
}
