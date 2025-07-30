<?php

namespace App\Providers;

use App\Livewire\Common\MessageService;
use Illuminate\Support\ServiceProvider;

class MessageServiceProvider extends ServiceProvider
{
    public function register()
    {
//        $this->app->bind('message.service', function () {
//            return new MessageService();
//        });
    }

    public function boot()
    {
        //
    }
}
