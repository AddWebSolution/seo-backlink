<?php

namespace App\Modules\MasterBacklink\Http\Controllers;
use App\Modules\MasterBacklink\Services\MasterBacklinkService;
use App\Modules\MasterBacklink\Http\Resources\MasterBacklinkResource;
use App\Modules\MasterBacklink\Http\Requests\StoreMasterBacklinkRequest;
use App\Modules\MasterBacklink\Http\Requests\UpdateMasterBacklinkRequest;

use Addweb\Base\Controller\BaseController;
use Illuminate\Http\Request;

class MasterBacklinkController extends BaseController
{
    public function __construct(protected MasterBacklinkService $service)
        {
            $this->classes = [
                'request' => [
                    'store' => StoreMasterBacklinkRequest::class,
                    'update' => UpdateMasterBacklinkRequest::class,
                ],
                'resource' => MasterBacklinkResource::class,
            ];
        }

    public function importMasterBacklinks(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        return $this->service->processImport($request->file('file'));
    }

    public function masterBacklinksImportTemplateDownload()
    {
        return $this->service->downloadTemplate();
    }
}
