<?php

namespace App\Modules\User\Http\Controllers;

use App\Traits\HasProfilePicUpload;
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

    public function updateProfilePic(UploadProfilePicRequest $request)
    {
        $user = auth()->user();

        $this->uploadProfilePic($user, $request->file('profile_pic'), 'user');
        return response()->json(['success' => true]);
    }
}
