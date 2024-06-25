<?php

namespace App\Http\Requests\Customers;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:100',
            'email'    => 'nullable|email|unique:customers,email',
            'password' => 'required|min:6',
        ];
    }
}
