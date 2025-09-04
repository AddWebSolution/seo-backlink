<?php

namespace App\Modules\ClientDomain\Services;

use Addweb\Base\Services\BaseService;
use App\Modules\ClientDomain\Models\ClientDomain;

class ClientDomainService extends BaseService
{
    public array $searchFields = [
        'title'       => [],
        'source_url'  => [],
        'target_url'  => [],
        'country'     => [],
    ];

    public array $filters = [
        'title'   => ['filter' => 'contain'],
        'status'  => ['filter' => 'exact'],     // 1 = Available, 2 = Unavailable
        'country' => ['filter' => 'contain'],   // partial match
    ];

    public function __construct()
    {
        $this->object = new ClientDomain();
    }
}
