<?php 
namespace Megaads\Trapman;

use Illuminate\Support\ServiceProvider;
use Megaads\Trapman\Service\SendEmailService;

class TrapmanServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishConfig();
    }

    public function register()
    {
        $this->app->singleton('sendEmailService', function() {
            return new SendEmailService();
        });
    }

    private function publishConfig()
    {
        $path = $this->getConfigPath();
        $this->publishes([$path => config_path('trapman.php')], 'config');
    }

    private function getConfigPath()
    {
        return __DIR__.'/../config/trapman.php';
    }

}