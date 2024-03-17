<?php

namespace App\Http\Requests\Attributes;

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
        $rules = [
            'name'     => [
                'required',
                'string',
                'max:255',
                Rule::unique('attributes', 'name')->ignore($this->route('attribute')->id)
            ],
            'group_id' => 'required|exists:attribute_groups,id',
        ];


        return $rules;
    }

}
