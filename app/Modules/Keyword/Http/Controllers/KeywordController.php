<?php

namespace App\Modules\Keyword\Http\Controllers;

use Illuminate\Http\Request;
use Addweb\Base\Controller\BaseController;
use Addweb\Base\Services\Hooks\Hooks;
use App\Modules\Keyword\Services\KeywordService;
use App\Modules\Keyword\Http\Resources\KeywordResource;
use App\Modules\Keyword\Http\Requests\StoreKeywordRequest;
use App\Modules\Keyword\Http\Requests\UpdateKeywordRequest;
use App\Modules\Keyword\Models\Keyword;

class KeywordController extends BaseController
{   
    
    public function __construct(protected KeywordService $service)
    {
        $this->classes = [
            'request' => [
                'store' => StoreKeywordRequest::class,
                'update' => UpdateKeywordRequest::class,
            ],
            'resource' => KeywordResource::class,
        ];
    }

    public function keywordImportTemplateDownload()
    {
        return $this->service->keywordImportTemplateDownload();
    }

    public function keywordImport(Request $request)
    {
        try {
            $file = $request->file('file');

            if (!$file || !$file->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file upload',
                    'data' => null
                ], 422);
            }

            $result = $this->service->keywordImport($file->getPathname());

            return response()->json([
                'success' => true,
                'message' => 'Keywords imported successfully',
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Keyword import failed. Please check the file and try again.',
                'data' => null,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function keywordList()
    {   
        $keywords = Keyword::with('domain:id,title,target_url')
            ->where('status', 1)
            ->orderBy('client_domain_id')
            ->get()
            ->groupBy('client_domain_id')
            ->map(function ($group, $domainId) {
                $domain = $group->first()->domain;

                return [
                    'domain_id'   => $domain?->id,
                    'domain'      => $domain?->title,
                    'domain_url'  => $domain?->target_url,
                    'keywords'    => $group->map(function ($keyword) {
                        return [
                            'id'      => $keyword->id,
                            'keyword' => $keyword->keyword,
                        ];
                    })->values(),
                ];
            })->values();

        return response()->json([
            'success'  => true,
            'domains'  => $keywords,
        ]);
    }



    public function keywordHistory(Request $request, $id)
    {
        $perPage = $request->get('per_page', 10);

        $filters = [
            'search'          => $request->get('search'),
            'status'          => $request->get('status'),
        ];
        return $this->service->getKeywordHistory($id, $perPage, $filters);
    }
}
