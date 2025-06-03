<?php

namespace App\V1\Requests\Orders;

use App\V1\Requests\ValidatorBase;

class CheckStockRequest extends ValidatorBase
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'items'                      => 'required|array',
            'items.*'                    => 'required',
            'items.*.product_id'         => 'required|numeric',
            'items.*.product_variant_id' => 'nullable|numeric',
            'lang'                       => 'nullable|string',
        ];
    }
}
