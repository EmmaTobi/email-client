<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\EmailContract;
use App\Services\EmailService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(EmailContract::class, function(){
            return new EmailService();
        });
    }

}
