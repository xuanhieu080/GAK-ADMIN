<?php

namespace App\Http\Controllers;

use App\Http\Requests\SeoContents\StoreRequest;
use App\Http\Requests\SeoContents\UpdateRequest;
use App\Models\SeoContent;
use App\Services\SeoContent\SeoContentService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SeoContentController extends Controller
{
    /**
     * The service instance
     * @var SeoContentService
     */
    protected SeoContentService $seoContentService;

    /**
     * Constructor
     */
    public function __construct(SeoContentService $seoContentService)
    {
        $this->seoContentService = $seoContentService;
    }

    /**
     * Display a listing of the resource.
     * @return JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request)
    {
//        $this->authorize('list', SeoContent::class);

        return $this->seoContentService->index($request->all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function create()
    {
//        $this->authorize('create', SeoContent::class);

        return $this->responseDataSuccess(['properties' => $this->properties()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest  $request
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(StoreRequest $request)
    {
//        $this->authorize('create', SeoContent::class);

        $input = $request->validated();
        $record = $this->seoContentService->create($input);
        if (!is_null($record)) {
            return $this->responseStoreSuccess(['model' => $record]);
        } else {
            return $this->responseStoreFail();
        }
    }

    /**
     *  Show the form for editing the specified resource.
     *
     * @param  SeoContent  $seo_content
     *
     * @return SeoContentResource|JsonResponse
     * @throws AuthorizationException
     */
    public function show(SeoContent $seo_content)
    {
//        $this->authorize('view', SeoContent::class);

        $model = $this->seoContentService->get($seo_content);
        return $this->responseDataSuccess(['model' => $model, 'properties' => $this->properties()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  SeoContent  $seo_content
     *
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function edit(SeoContent $seo_content)
    {
//        $this->authorize('edit', SeoContent::class);

        return $this->show($seo_content);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  SeoContent  $seo_content
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(UpdateRequest $request, SeoContent $seo_content)
    {
//        $this->authorize('edit', SeoContent::class);

        $data = $request->validated();
        if ($item = $this->seoContentService->updateItem($seo_content, $data)) {
            return $this->responseUpdateSuccess(['model' => $item]);
        } else {
            return $this->responseUpdateFail();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(SeoContent $seo_content)
    {
//        $this->authorize('delete', SeoContent::class);

        if ($this->seoContentService->delete($seo_content)) {
            return $this->responseDeleteSuccess(['model' => $seo_content]);
        }

        return $this->responseDeleteFail();

    }

    /**
     * Render properties
     * @return array
     */
    public function properties()
    {
        return [];
    }
}
