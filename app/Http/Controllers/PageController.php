<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\PageResource;
use App\Http\Requests\Pages\StoreRequest;
use App\Http\Requests\Pages\UpdateRequest;
use App\Http\Requests\DestroyUserRequest;
use App\Services\Page\PageService;

class PageController extends Controller
{
    /**
     * The service instance
     * @var PageService
     */
    protected PageService $pageService;

    /**
     * Constructor
     */
    public function __construct(PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    /**
     * Display a listing of the resource.
     * @return JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('list', Page::class);

        return $this->pageService->index($request->all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function create()
    {
        $this->authorize('create', Page::class);

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
        $this->authorize('create', Page::class);

        $input = $request->validated();
        $record = $this->pageService->create($input);
        if (!is_null($record)) {
            return $this->responseStoreSuccess(['model' => $record]);
        } else {
            return $this->responseStoreFail();
        }
    }

    /**
     *  Show the form for editing the specified resource.
     *
     * @param  Page  $page
     *
     * @return PageResource|JsonResponse
     * @throws AuthorizationException
     */
    public function show(Page $page)
    {
        $this->authorize('view', Page::class);

        $model = $this->pageService->get($page);
        return $this->responseDataSuccess(['model' => $model, 'properties' => $this->properties()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Page  $page
     *
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function edit(Page $page)
    {
        $this->authorize('edit', Page::class);

        return $this->show($page);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  Page  $page
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(UpdateRequest $request, Page $page)
    {
        $this->authorize('edit', Page::class);

        $data = $request->validated();
        if ($item = $this->pageService->updateItem($page, $data)) {
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
    public function destroy(DestroyUserRequest $request, Page $page)
    {
        $this->authorize('delete', Page::class);

        if ($this->pageService->delete($page)) {
            return $this->responseDeleteSuccess(['model' => $page]);
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
