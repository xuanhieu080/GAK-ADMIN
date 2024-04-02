<?php

namespace App\Http\Requests\Attributes;

use App\Http\Requests\BaseRequest;
use App\Rules\Color;
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
        $rules = [
            'name'     => 'required|string|max:255|unique:attributes,name',
            'group_id' => 'nullable|exists:attribute_groups,id',
            'is_color' => 'nullable|in:true,false,1,0',
            'link'     => 'nullable|url|max:350',
        ];
        if (filter_var($this->is_color, FILTER_VALIDATE_BOOLEAN)) {
            $rules['color'] = [
                'required',
                'max:255',
                new Color()
            ];
        }

        return $rules;
    }
}
