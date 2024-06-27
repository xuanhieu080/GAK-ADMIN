<?php

namespace App\Http\Controllers;

use App\Http\Requests\Products\AttributeRequest;
use App\Http\Requests\Products\StoreReviewRequest;
use App\Http\Requests\Products\UpdateHighlightRequest;
use App\Http\Requests\Products\UpdateReviewRequest;
use App\Http\Requests\Products\UpdateVariantItemRequest;
use App\Http\Requests\Products\UpdateVariantMainItemRequest;
use App\Http\Requests\Products\UpdateVariantRequest;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ProductVariant;
use App\Models\ProductVariantMain;
use App\Models\Variant;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use App\Http\Requests\Products\StoreRequest;
use App\Http\Requests\Products\UpdateRequest;
use App\Http\Requests\DestroyUserRequest;
use App\Services\Product\ProductService;

class ProductController extends Controller
{
    /**
     * The service instance
     * @var ProductService
     */
    private ProductService $productService;

    /**
     * Constructor
     */
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Display a listing of the resource.
     * @return JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request)
    {
//        $this->authorize('list', Product::class);

        return $this->productService->index($request->all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function create()
    {
//        $this->authorize('create', Product::class);

        return $this->responseDataSuccess(['properties' => $this->properties()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(StoreRequest $request)
    {
//        $this->authorize('create', Product::class);

        $input = $request->validated();
        $record = $this->productService->create($input);
        if (!is_null($record)) {
            return $this->responseStoreSuccess(['model' => $record]);
        } else {
            return $this->responseStoreFail();
        }
    }

    /**
     *  Show the form for editing the specified resource.
     *
     * @param Product $product
     *
     * @return ProductResource|JsonResponse
     * @throws AuthorizationException
     */
    public function show(Product $product)
    {
//        $this->authorize('view', Product::class);

        $model = $this->productService->get($product);
        return $this->responseDataSuccess(['model' => $model, 'properties' => $this->properties()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Product $product
     *
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function edit(Product $product)
    {
//        $this->authorize('edit', Product::class);

        return $this->show($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Product $product
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(UpdateRequest $request, Product $product)
    {
//        $this->authorize('edit', Product::class);

        $data = $request->validated();
        if ($product = $this->productService->updateItem($product, $data)) {
            return $this->responseUpdateSuccess(['model' => $product, 'properties' => $this->properties()]);
        } else {
            return $this->responseUpdateFail();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Product $product
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function attribute(AttributeRequest $request, Product $product)
    {
//        $this->authorize('attribute', Product::class);

        $data = $request->validated();
        if ($product = $this->productService->attribute($product, $data)) {
            return $this->responseUpdateSuccess(['model' => $product, 'properties' => $this->properties()]);
        } else {
            return $this->responseUpdateFail();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Product $product
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function getAttribute(Product $product)
    {
//        $this->authorizeize('attribute', Product::class);

        return $this->productService->getAttribute($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(DestroyUserRequest $request, Product $product)
    {
//        $this->authorize('delete', Product::class);

        if ($this->productService->delete($product)) {
            return $this->responseDeleteSuccess(['model' => $product]);
        }

        return $this->responseDeleteFail();
    }

    public function productVariantSync(Product $product)
    {
//        $this->authorize('product-variant', Product::class);

        return $this->productService->productVariantSync($product);
    }

    public function productVariant(Product $product)
    {
//        $this->authorize('product-variant', Product::class);

        return $this->productService->productVariant($product);
    }

    public function productVariantMain(Product $product)
    {
//        $this->authorize('product-variant', Product::class);

        return $this->productService->productVariantMain($product);
    }

    /**
     * @throws \Exception
     */
    public function updateProductVariant(UpdateVariantRequest $request, Product $product)
    {
//        $this->authorize('product-variant', Product::class);

        $input = $request->validated();
        if ($this->productService->updateProductVariant($product, $input)) {
            return $this->responseUpdateSuccess();
        }

        return $this->responseUpdateFail();
    }

    /**
     * @throws \Exception
     */
    public function createReview(StoreReviewRequest $request, Product $product)
    {
//        $this->authorize('product-variant', Product::class);

        $input = $request->validated();
        if ($this->productService->createReview($product, $input)) {
            return $this->responseStoreSuccess();
        }

        return $this->responseUpdateFail();
    }

    /**
     * @throws \Exception
     */
    public function getReview(Product $product, Request $request)
    {
//        $this->authorizeize('attribute', Product::class);

        $input = $request->all();
        return $this->productService->getReview($product, $input);
    }

    /**
     * @throws \Exception
     */
    public function getReviewItem(Product $product, ProductReview $productReview)
    {
//        $this->authorizeize('attribute', Product::class);
//        $this->authorize('view', Product::class);

        $model = $this->productService->getReviewItem($product,$productReview);
        return $this->responseDataSuccess(['model' => $model, 'properties' => $this->properties()]);
    }

    /**
     * @throws \Exception
     */
    public function updateReview(UpdateReviewRequest $request, Product $product, ProductReview $product_review)
    {
//        $this->authorize('product-variant', Product::class);

        $input = $request->validated();
        if ($this->productService->updateReview($product, $product_review, $input)) {
            return $this->responseUpdateSuccess();
        }

        return $this->responseUpdateFail();
    }

    public function deleteReview(Product $product, ProductReview $product_review)
    {
//        $this->authorize('delete', Product::class);

        if ($this->productService->deleteReview($product, $product_review)) {
            return $this->responseDeleteSuccess(['model' => $product_review]);
        }

        return $this->responseDeleteFail();
    }

    /**
     * @throws \Exception
     */
    public function updateProductVariantItem(UpdateVariantItemRequest $request, ProductVariant $product_variant)
    {
//        $this->authorize('product-variant', Product::class);

        $input = $request->validated();
        if ($this->productService->updateProductVariantItem($product_variant, $input)) {
            return $this->responseUpdateSuccess();
        }

        return $this->responseUpdateFail();
    }

    /**
     * @throws \Exception
     */
    public function updateProductVariantMainItem(UpdateVariantMainItemRequest $request, Product $product, ProductVariantMain $product_variant_main)
    {
//        $this->authorize('product-variant', Product::class);
        $input = $request->validated();

        if ($this->productService->updateProductVariantMainItem($product, $product_variant_main, $input)) {
            return $this->responseUpdateSuccess();
        }

        return $this->responseUpdateFail();
    }

    /**
     * @throws \Exception
     */
    public function updateHighlight(UpdateHighlightRequest $request, Product $product)
    {
//        $this->authorize('product-variant', Product::class);

        $input = $request->validated();
        if ($item = $this->productService->updateHighlight($product, $input)) {
            return $this->responseUpdateSuccess(['model' => $item]);
        }

        return $this->responseUpdateFail();
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
