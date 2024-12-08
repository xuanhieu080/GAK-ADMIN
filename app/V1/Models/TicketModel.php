<?php

namespace App\V1\Models;

use App\Models\Ticket;
use App\V1\Resources\TicketResource;
use Illuminate\Support\Arr;

class TicketModel extends AbstractModel
{

    /**
     * Comment constructor.
     */
    public function __construct()
    {
        $model = new Ticket();
        parent::__construct($model);
    }

    public function store(array $data) {
       return $this->model::query()->create($data);
    }
}
