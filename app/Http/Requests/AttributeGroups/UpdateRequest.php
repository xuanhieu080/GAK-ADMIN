<?php

namespace App\Http\Requests\AttributeGroups;

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
            'name'    => [
                'required',
                'string',
                'max:255',
                Rule::unique('attribute_groups', 'name')->ignore($this->route('attribute_group')->id)
            ],
            'name_en' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('attribute_groups', 'name_en')->ignore($this->route('attribute_group')->id)
            ],

            'priority' => 'required|numeric|min:0|max:1000',
            'is_color' => 'nullable|in:1,0,true,false',
            'is_main'  => 'nullable|in:1,0,true,false',
            'link'     => 'nullable|url|max:255',
            'link_en'  => 'nullable|url|max:255',
        ];
    }

}
