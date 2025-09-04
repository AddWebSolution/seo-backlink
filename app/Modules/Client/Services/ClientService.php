<?php

namespace App\Modules\Client\Services;

use Addweb\Base\Services\BaseService;
use App\Modules\Client\Models\Client;

class ClientService extends BaseService
{
    public array $searchFields = [
        'title' => [],
    ];

    public array $filters = [
        'title' => ['filter' => 'contain'],
    ];

    public function __construct()
    {
        $this->object = new Client();
    }
}
