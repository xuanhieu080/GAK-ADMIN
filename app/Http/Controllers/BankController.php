<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use App\Http\Requests\Posts\StoreRequest;
use App\Http\Requests\Posts\UpdateRequest;
use App\Http\Requests\DestroyUserRequest;
use App\Services\Bank\BankService;

class BankController extends Controller
{
    /**
     * The service instance
     * @var BankService
     */
    protected BankService $bankService;

    /**
     * Constructor
     */
    public function __construct(bankService $bankService)
    {
        $this->bankService = $bankService;
    }

    /**
     * Display a listing of the resource.
     * @return JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request)
    {
//        $this->authorize('list', Post::class);

        return $this->bankService->index($request->all());
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
