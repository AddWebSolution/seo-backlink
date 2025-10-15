<?php

return [
    App\Providers\AppServiceProvider::class,
    Spatie\Permission\PermissionServiceProvider::class,
    Addweb\Base\Provider\BaseServiceProvider::class,
    App\Modules\Client\Providers\ClientServiceProvider::class,
    App\Modules\User\Providers\UserServiceProvider::class,
    App\Modules\ClientDomain\Providers\ClientDomainServiceProvider::class,
    App\Modules\Report\Providers\ReportServiceProvider::class,
    App\Modules\BacklinkDatum\Providers\BacklinkDatumServiceProvider::class,
    App\Modules\Keyword\Providers\KeywordServiceProvider::class,
    App\Modules\KeywordReport\Providers\KeywordReportServiceProvider::class,
    App\Modules\KeywordDatum\Providers\KeywordDatumServiceProvider::class,
    App\Modules\RivalDomain\Providers\RivalDomainServiceProvider::class,    
    App\Modules\Dashboard\Providers\DashboardServiceProvider::class,
    App\Modules\BacklinkHistory\Providers\BacklinkHistoryServiceProvider::class,
];
