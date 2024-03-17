<?php

namespace App\Http\Requests\Attributes;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

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
            'name'     => 'required|string|max:255|unique:attributes,name',
            'group_id' => 'nullable|exists:attribute_groups,id',
        ];
    }
}
