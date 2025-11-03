<?php

namespace App\Modules\User\Services;

use App\Modules\User\Models\User;
use Addweb\Base\Services\BaseService;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService extends BaseService
{
    public array $searchFields = [
        'name' => [],
        'email' => [],
        'status' => [],
        'company_name' => [],
        'phone' => [],
    ];

    public array $filters = [
        'status' => ['filter' =>  ''],

    ];

    public string $sortField = 'id';
    public string $sortOrder = 'desc';

    protected function loadRelations(): void
    {
        $route = request()->route()?->getName();

        if ($route === 'client.get') {
            $this->query->where('role', '2');
            $this->loadExtraRelation();
        } else if ($route === 'user.get') {
            $this->query->whereNotIn('role', ['1', '2']);
            $this->loadExtraRelation();
        }
    }

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

    public function userList(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = User::with(['roles:id,name'])
            ->whereDoesntHave('roles', function ($q) {
                $q->whereIn('id', [1, 2]);
            })
            ->orderBy('name');

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

        $users = $query->paginate($perPage);

        $users->getCollection()->transform(function ($user) {
            $role = $user->roles->first();
            $user->role = $role ? [
                'id' => $role->id,
                'name' => $role->name,
            ] : null;

            unset($user->roles);

            return $user;
        });

        return $users;
    }


       public function clientList(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = User::with(['roles:id,name'])
            ->whereDoesntHave('roles', function ($q) {
                $q->whereIn('id', [2]);
            })
            ->orderBy('name');

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

        $users = $query->paginate($perPage);

        $users->getCollection()->transform(function ($user) {
            $role = $user->roles->first();
            $user->role = $role ? [
                'id' => $role->id,
                'name' => $role->name,
            ] : null;

            unset($user->roles);

            return $user;
        });

        return $users;
    }

}
