<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\Categories\StoreRequest;
use App\Http\Requests\Categories\UpdateRequest;
use App\Http\Requests\DestroyUserRequest;
use App\Services\Product\CategoryService;

class CategoryController extends Controller
{
    /**
     * The service instance
     * @var CategoryService
     */
    private CategoryService $categoryService;

    /**
     * Constructor
     */
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the resource.
     * @return JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request)
    {
//        $this->authorize('list', Category::class);

        return $this->categoryService->index($request->all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function create()
    {
//        $this->authorize('create', Category::class);

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
//        $this->authorize('create', Category::class);

        $input = $request->validated();
        $record = $this->categoryService->create($input);
        if (!is_null($record)) {
            return $this->responseStoreSuccess(['model' => $record]);
        } else {
            return $this->responseStoreFail();
        }
    }

    /**
     *  Show the form for editing the specified resource.
     *
     * @param  Category  $category
     *
     * @return CategoryResource|JsonResponse
     * @throws AuthorizationException
     */
    public function show(Category $category)
    {
//        $this->authorize('view', Category::class);

        $model = $this->categoryService->get($category);
        return $this->responseDataSuccess(['model' => $model, 'properties' => $this->properties()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Category  $category
     *
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function edit(Category $category)
    {
//        $this->authorize('edit', Category::class);

        return $this->show($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  Category  $category
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(UpdateRequest $request, Category $category)
    {
//        $this->authorize('edit', Category::class);

        $data = $request->validated();
        if ($item = $this->categoryService->update($category, $data)) {
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
    public function destroy(DestroyUserRequest $request, Category $category)
    {
//        $this->authorize('delete', Category::class);

        if ($this->categoryService->delete($category)) {
            return $this->responseDeleteSuccess(['model' => $category]);
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
