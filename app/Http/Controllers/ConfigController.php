<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadImageRequest;
use App\Models\Config;
use App\Supports\HasImage;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\ConfigResource;
use App\Http\Requests\Categories\StoreRequest;
use App\Http\Requests\Configs\UpdateRequest;
use App\Http\Requests\DestroyUserRequest;
use App\Services\Config\ConfigService;

class ConfigController extends Controller
{
    /**
     * The service instance
     * @var ConfigService
     */
    private ConfigService $configService;

    /**
     * Constructor
     */
    public function __construct(ConfigService $configService)
    {
        $this->configService = $configService;
    }

    /**
     * Display a listing of the resource.
     * @return JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('list', Config::class);

        return $this->configService->index($request->all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function create()
    {
        $this->authorize('create', Config::class);

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
    public function store(Request $request)
    {
//        $this->authorize('create', Config::class);
//
//        $input = $request->validated();
//        $record = $this->configService->create($input);
//        if (!is_null($record)) {
//            return $this->responseStoreSuccess(['model' => $record]);
//        } else {
//            return $this->responseStoreFail();
//        }
    }

    /**
     *  Show the form for editing the specified resource.
     *
     * @param  Config  $config
     *
     * @return ConfigResource|JsonResponse
     * @throws AuthorizationException
     */
    public function show(Config $config)
    {
        $this->authorize('view', Config::class);

        $model = $this->configService->get($config);
        return $this->responseDataSuccess(['model' => $model, 'properties' => $this->properties()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Config  $config
     *
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function edit(Config $config)
    {
        $this->authorize('edit', Config::class);

        return $this->show($config);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  Config  $config
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(UpdateRequest $request, Config $config)
    {
        $this->authorize('edit', Config::class);

        $data = $request->validated();
        if ($this->configService->update($config, $data)) {
            return $this->responseUpdateSuccess(['model' => $config->fresh()]);
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
    public function destroy(DestroyUserRequest $request, Config $config)
    {
//        $this->authorize('delete', Config::class);
//
//        if ($this->configService->delete($config)) {
//            return $this->responseDeleteSuccess(['model' => $config]);
//        }
//
//        return $this->responseDeleteFail();

    }

    /**
     * Render properties
     * @return array
     */
    public function properties()
    {
        return [];
    }

    public function uploadImage(UploadImageRequest $request)
    {
        try {
            $image = HasImage::addImage($request->file('image'));
            $link = HasImage::getImage($image);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
        return response()->json(['link' => $link]);
    }
}
