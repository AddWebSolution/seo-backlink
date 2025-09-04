<?php

namespace App\Modules\User\Services;

use App\Modules\User\Models\User;
use Addweb\Base\Services\BaseService;
use Illuminate\Support\Facades\Storage;

class UserService extends BaseService
{
    public array $searchFields = [
        'title' => [],
    ];

    public array $filters = [
        'title' => ['filter' => 'contain'],
    ];

    public function __construct()
    {
        $this->object = new User();
    }
}
