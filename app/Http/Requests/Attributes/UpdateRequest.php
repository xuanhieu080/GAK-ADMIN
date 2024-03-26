<?php

namespace App\Http\Requests\Attributes;

use App\Http\Requests\BaseRequest;
use App\Rules\Color;
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
            'is_color' => 'nullable|in:true,false,1,0',
        ];

        if (filter_var($this->is_color, FILTER_VALIDATE_BOOLEAN)) {
            $rules['color'] = [
                'required',
                'max:255',
                new Color()
            ];
        }

        return $rules;


        return $rules;
    }

}
