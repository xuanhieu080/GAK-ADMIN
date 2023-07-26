<?php

namespace App\Http\Requests\Customers;

use App\Http\Requests\BaseRequest;

class RechargeRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'amount'      => 'required|numeric|min:1000|max:999999999999',
//            'code'        => 'required|string|min:3|max:255|unique:customer_recharges,code',
//            'description' => 'nu|string|min:3|max:255',
            'bank_id'        => 'required|exists:banks,id',
            'customer_id'        => 'required|exists:customers,id',
        ];
    }

}
