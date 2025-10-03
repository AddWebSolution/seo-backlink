<?php

namespace App\Modules\User\Http\Controllers;

use App\Traits\HasProfilePicUpload;
use Illuminate\Http\Request;
use App\Modules\User\Models\User;
use Addweb\Base\Controller\BaseController;
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

    public function clientList()
    {
        $clients = User::select('id', 'name', 'company_name', 'phone', 'email')
            ->where('role', '3')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'clients' => $clients,
        ]);
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
}
