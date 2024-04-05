<?php

namespace App\Http\Requests\Customers;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends BaseRequest
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
//            'username' => 'required|string|max:100',
            'email'      => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('customers', 'email')->ignore($this->route('customer')->id)
            ],
            //            'roles' => 'required|array|exists:roles,name',
            'password' => 'nullable|min:6'
        ];
    }

}
