<?php

namespace App\Http\Requests\PageGroups;

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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('page_groups', 'name')->ignore($this->route('page_group')->id)
            ],

            'column' => 'required|numeric|min:1|max:10',
        ];
    }

}
