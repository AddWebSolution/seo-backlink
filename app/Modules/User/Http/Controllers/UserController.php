<?php

namespace App\Modules\User\Http\Controllers;

use App\Modules\ClientDomain\Models\ClientDomain;
use App\Traits\HasProfilePicUpload;
use App\Modules\User\Http\Requests\RegisterUserRequest;
use Illuminate\Http\Request;
use App\Modules\User\Models\User;
use Addweb\Base\Controller\BaseController;
use App\Enums\UserStatus;
use App\Modules\User\Services\UserService;
use App\Modules\User\Http\Resources\UserResource;
use App\Modules\User\Http\Requests\StoreUserRequest;
use App\Modules\User\Http\Requests\UpdateUserRequest;
use App\Modules\User\Http\Requests\UploadProfilePicRequest;

class UserController extends BaseController
{
    use HasProfilePicUpload;

    public function __construct(protected UserService $service)
    {
        $this->classes = [
            'request' => [
                'store' => StoreUserRequest::class,
                'update' => UpdateUserRequest::class,
            ],
            'resource' => UserResource::class,
        ];
    }


    public function createClient(RegisterUserRequest $request)
    {
        $user = $this->service->clientCreate($request->validated());

        return response()->json([
            'message' => 'Client registered successfully',
            'user'    => $user
        ], 201);
    }

    public function createUser(RegisterUserRequest $request)
    {
        $user = $this->service->userCreate($request->validated());

        return response()->json([
            'message' => 'User registered successfully',
            'user'    => $user
        ], 201);
    }

    public function clientList()
    {
        $clients = User::select('id', 'name', 'company_name', 'phone', 'email')
            ->where('role', '2')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'clients' => $clients,
        ]);
    }
     public function UserList(Request $request)
    {   
        $filters = $request->only(['search', 'status']);
        $perPage = $request->input('per_page', 10);

        $users = $this->service->UserList($filters, $perPage);

        return response()->json([
            'data' => [
                'resource' => $users->items(),
                'pagination' => [
                    'total' => $users->total(),
                    'currentPage' => $users->currentPage(),
                    'perPage' => $users->perPage(),
                    'lastPage' => $users->lastPage(),
                ],
            ],
            'message' => 'Resource Fetched',
            'success' => true,
        ]);
    }


    public function clientDomains()
    {
        return User::query()
            ->where('status', UserStatus::ACTIVE)
            ->where('role', '2')
            ->with([
                'clientDomains' => function ($query) {
                    $query->select('id', 'client_id', 'title', 'target_url') 
                        ->where('status', 1) 
                        ->with(['rivalDomains' => function ($q) {
                            $q->select('id', 'client_domain_id', 'title', 'target_url')
                                ->where('status', 1); 
                        }]);
                }
            ])
            ->get(['id', 'name']); 
    }

    public function ClientListByRole(Request $request)
    {   
        $perPage = $request->input('perPage', 15);
        $filters = $request->only(['search', 'status']);

        $clients = $this->service->listByRoleWithFilters($perPage, $filters);

        return response()->json([
             $clients
        ]);
    }

    public function updateProfilePic(UploadProfilePicRequest $request)
    {
        $user = auth()->user();

        $this->uploadProfilePic($user, $request->file('profile_pic'), 'user');
        return response()->json(['success' => true]);
    }

    public function listAssignableUsers($domainId)
    {
        $domain = ClientDomain::findOrFail($domainId);

        $users = User::where('role', '!=', '2')
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        $assigned = $domain->users()
            ->select('users.id', 'users.name', 'users.email')
            ->orderBy('users.name')
            ->get();

        return response()->json([
            'status' => true,
            'users' => $users,
            'assigned' => $assigned
        ]);
    }


}
