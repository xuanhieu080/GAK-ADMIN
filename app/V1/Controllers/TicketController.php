<?php

namespace App\V1\Controllers;

;
use App\Models\Ticket;
use App\V1\Models\TicketModel;
use App\V1\Requests\Tickets\CreateRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    protected $model;

    public function __construct()
    {
        $this->model = new TicketModel();
    }

    public function create(CreateRequest $request)
    {
        $input = $request->validated();

        $this->model->store($input);

        return response()->json(['message' => 'Gửi yêu cầu thành công'], 200);
    }
}
