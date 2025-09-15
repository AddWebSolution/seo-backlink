<?php

namespace App\Modules\Keyword\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Modules\Keyword\Events\AfterKeywordStoreEvent;
use App\Modules\Keyword\Events\BeforeKeywordStoreEvent;
use App\Modules\Keyword\Events\AfterKeywordUpdateEvent;
use App\Modules\Keyword\Events\BeforeKeywordUpdateEvent;
use App\Modules\Keyword\Events\AfterKeywordDeleteEvent;
use App\Modules\Keyword\Events\BeforeKeywordDeleteEvent;
use App\Modules\Keyword\Events\AfterKeywordRestoreEvent;
use App\Modules\Keyword\Events\BeforeKeywordRestoreEvent;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        BeforeKeywordStoreEvent::class => [],
        AfterKeywordStoreEvent::class => [],
        BeforeKeywordUpdateEvent::class => [],
        AfterKeywordUpdateEvent::class => [],
        BeforeKeywordDeleteEvent::class => [],
        AfterKeywordDeleteEvent::class => [],
        BeforeKeywordRestoreEvent::class => [],
		AfterKeywordRestoreEvent::class => [],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}