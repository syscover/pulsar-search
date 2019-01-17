<?php namespace Syscover\Search;

use Illuminate\Support\ServiceProvider;
use Laravel\Scout\EngineManager;

class SearchServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        resolve(EngineManager::class)->extend('pulsar-search', function () {
            return new PulsarSearchEngine;
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}