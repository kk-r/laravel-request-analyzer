<?php

namespace kkr\laravelRequestAnalyze\Providers;

use Illuminate\Support\ServiceProvider;

class RequestAnalyzeServiceProvider extends ServiceProvider
{

    protected $defer = false;
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->publishes($this->publishConfig());
        //
        $this->mergeConfigFrom(
            __DIR__.'/../config/requestAnalyzer.php', 'requestAnalyzer'
        );
    }

    protected function publishConfig()
    {
        return [
            realpath(__DIR__.
                '/../config/requestAnalyzer.php') =>
                (function_exists('config_path') ?
                    config_path('requestAnalyzer.php') :
                    base_path('config/requestAnalyzer.php')),
        ];
    }
}
