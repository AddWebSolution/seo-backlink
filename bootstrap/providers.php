<?php

return [
    App\Providers\AppServiceProvider::class,
    Spatie\Permission\PermissionServiceProvider::class,
    Addweb\Base\Provider\BaseServiceProvider::class,
    App\Modules\Client\Providers\ClientServiceProvider::class,
    App\Modules\User\Providers\UserServiceProvider::class,
    App\Modules\ClientDomain\Providers\ClientDomainServiceProvider::class,
    App\Modules\Report\Providers\ReportServiceProvider::class,
    App\Modules\Backlinkreport\Providers\BacklinkreportServiceProvider::class,
    App\Modules\Keyword\Providers\KeywordServiceProvider::class,
    App\Modules\KeywordReport\Providers\KeywordReportServiceProvider::class
];
