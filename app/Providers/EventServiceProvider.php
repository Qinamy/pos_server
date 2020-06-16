<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Log;
use DB;
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        if(env('SQL_LOG',false)){


            DB::listen(function($query) {

                $sql =  $query->sql;
                $bindings =  $query->bindings;
                $time =  $query->time;


                if(strpos($sql,'kl_jobs') == false){
                    $bindings_str = json_encode($bindings);
                    Log::info('sql: ' . $sql);
                    Log::info('bindings:' . $bindings_str);
                    Log::info('time: ' . $time);

                    if(intval($time) >= 30){
                        Log::notice('heavy query sql:' . $sql);
                        Log::notice('heavy query bindings:' . $bindings_str);
                        Log::notice('heavy query time:' . $time);
                    }
                }

            });
        }
    }
}
