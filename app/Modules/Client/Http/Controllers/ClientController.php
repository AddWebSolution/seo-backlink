<?php

namespace App\Modules\Client\Http\Controllers;

use App\Traits\HasProfilePicUpload;
use App\Modules\Client\Models\Client;
use Addweb\Base\Controller\BaseController;
use App\Modules\Client\Services\ClientService;
use App\Modules\Client\Http\Resources\ClientResource;
use App\Modules\Client\Http\Requests\UploadPicRequest;
use App\Modules\Client\Http\Requests\StoreClientRequest;
use App\Modules\Client\Http\Requests\UpdateClientRequest;

class ClientController extends BaseController
{
    use HasProfilePicUpload;

    public function __construct(protected ClientService $service)
    {
        $this->classes = [
            'request' => [
                'store' => StoreClientRequest::class,
                'update' => UpdateClientRequest::class,
            ],
            'resource' => ClientResource::class,
        ];
    }

    public function clientList()
    {
        $clients = Client::select('id', 'name', 'company_name','website','email')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'clients' => $clients,
        ]);
    }

    public function updateProfilePic(UploadPicRequest $request, $id)
    {
        $client = Client::findOrFail($id);

        $this->uploadProfilePic($client, $request->file('profile_pic'), 'client'); // different folder
        return response()->json(['success' => true]);
    }
}
