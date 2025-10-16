<?php

namespace App\Modules\BacklinkHistory\Http\Controllers;
use App\Modules\BacklinkHistory\Services\BacklinkHistoryService;
use App\Modules\BacklinkHistory\Http\Resources\BacklinkHistoryResource;
use App\Modules\BacklinkHistory\Http\Requests\StoreBacklinkHistoryRequest;
use App\Modules\BacklinkHistory\Http\Requests\UpdateBacklinkHistoryRequest;

use Addweb\Base\Controller\BaseController;

class BacklinkHistoryController extends BaseController
{
    public function __construct(protected BacklinkHistoryService $service)
        {
            $this->classes = [
                'request' => [
                    'store' => StoreBacklinkHistoryRequest::class,
                    'update' => UpdateBacklinkHistoryRequest::class,
                ],
                'resource' => BacklinkHistoryResource::class,
            ];
        }


    public function fetchHistoryData($id){
        $data = $this->service->fetchHistoryData($id);
        return $data;
    }

    public function updateorcreate(StoreBacklinkHistoryRequest $request)
    {
        $data = $request->input();

        if (isset($data[0])) {
            $data = $data[0];
        }

        $record = $this->service->updateOrCreateHistory($data);
        
        return response()->json([
            'success' => true,
            'data' => $record
        ]);
    }

}
