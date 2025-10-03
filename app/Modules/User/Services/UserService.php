<?php

namespace App\Modules\User\Services;

use App\Modules\User\Models\User;
use Addweb\Base\Services\BaseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService extends BaseService
{
    public array $searchFields = [
        'name' => [],
    ];

    public array $filters = [
        'name' => ['filter' => 'default'],
        'email' => ['filter' => 'default']
    ];

    public function __construct()
    {
        $this->object = new User();
    }

    /**
     * Get clients paginated based on role and optional filters
     *
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function listByRoleWithFilters(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            $query = User::query();
        } elseif ($user->hasRole('client')) {
            $query = User::where('id', $user->id);
        } else {
            $query = User::whereRaw('0=1');
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($perPage);
    }
}
