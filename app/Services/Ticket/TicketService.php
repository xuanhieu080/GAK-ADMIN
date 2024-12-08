<?php

namespace App\Services\Ticket;

use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\V1\Models\AbstractModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class TicketService
{
    public $model;
    public function __construct()
    {
        $this->model = new Ticket();
    }

    /**
     * Get a single resource from the database
     * @param Ticket $ticket
     * @return TicketResource
     */
    public function get(Ticket $ticket)
    {
        return new TicketResource($ticket);
    }

    /**
     * Get resource index from the database
     * @param $query
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index($data)
    {
        $per_page = isset($data['per_page']) && is_numeric($data['per_page']) ? intval($data['per_page']) : 10;

        $query = Ticket::query();

        if (!empty($data['filters'])) {
            $this->filter($query, $data['filters']);
        }
        if (!empty($data['sort_by']) && !empty($data['sort'])) {
            $query = $query->orderBy($data['sort_by'], $data['sort']);
        } else {
            $query = $query->latest();
        }

        return TicketResource::collection($query->paginate($per_page));
    }

    private function filter(Builder &$query, $filters)
    {
        $query->filter(Arr::except($filters, []));
    }
}
