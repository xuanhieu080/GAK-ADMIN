<?php

namespace App\Http\Requests\Attributes;

use App\Http\Requests\BaseRequest;
use App\Models\AttributeGroup;
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
            'name_en'  => 'nullable|string|max:255|unique:attributes,name_en',
            'group_id' => 'nullable|exists:attribute_groups,id',
            //            'is_color' => 'nullable|in:true,false,1,0',
            //            'link'     => 'nullable|url|max:350',
        ];
        if (!empty($this->group_id)) {
            $group = AttributeGroup::find($this->group_id);
            if (!empty($group) && filter_var($group->is_color, FILTER_VALIDATE_BOOLEAN)) {
                $rules['color'] = [
                    'required',
                    'max:255',
                    new Color()
                ];
            }
        }

        return $rules;
    }
}
