<?php

namespace App\V1\Requests\Tickets;

use App\Rules\ProductNotVariant;
use App\Rules\ProductVariantExists;
use App\Rules\ProductVariantQty;
use App\V1\Requests\ValidatorBase;

class CreateRequest extends ValidatorBase
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|max:255',
            'phone'       => ['required', 'regex:/^(?:\+84|0)(3[2-9]|5[2|6|8|9]|7[0|6-9]|8[1-7]|9[0-9])[0-9]{7}$/'],
            'description' => 'required|string|max:3000',
        ];
    }
}
