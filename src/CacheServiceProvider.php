<?php


namespace Shetabit\ResponseCache;

use Shetabit\ResponseCache\Commands\PublishFile;
use Shetabit\ResponseCache\MakeCache;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;


class CacheServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Event::listen(RequestHandled::class, [MakeCache::class, 'handle']);

        if ($this->app->runningInConsole()) {
            $this->commands([
                PublishFile::class,
            ]);
        }

        $this->publishes([
            __DIR__.'/Caches' => app_path('Caches'),
        ],'files');
    }
}
