<?php

namespace App\Http\Controllers;


use App\Models\Ticket;
use App\Services\Ticket\TicketService;
use App\V1\Controllers\Controller;
use App\V1\Models\TicketModel;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    protected $ticketService;

    public function __construct()
    {
        $this->ticketService = new TicketService();
    }

    /**
     * Display a listing of the resource.
     * @throws AuthorizationException
     */
    public function index(Request $request)
    {
        return $this->ticketService->index($request->all());
    }

    public function show(Ticket $ticket)
    {
        $model = $this->ticketService->get($ticket);
        return $this->responseDataSuccess(['model' => $model, 'properties' => $this->properties()]);
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
