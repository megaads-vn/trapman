<?php 
namespace Megaads\Trapman;

use Illuminate\Support\ServiceProvider;
use Megaads\Trapman\Service\SendEmailService;

class TrapmanServiceProvider extends ServiceProvider
{
    public function boot()
    {

    }

    public function register()
    {
        $this->app->singleton('sendEmailService', function() {
            return new SendEmailService();
        });
    }

}