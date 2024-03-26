<?php

namespace App\Http\Requests\Products;

use App\Http\Requests\BaseRequest;
use App\Models\ProductDetail;
use Illuminate\Validation\Rule;

class AttributeRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'details'                              => 'nullable|array',
            'details.*'                            => 'nullable',
            'details.*.attribute_id'               => 'required|exists:attributes,id',
            'details.*.attribute_group_id'         => 'required|exists:attribute_groups,id',
            'detail_currents'                      => 'nullable|array',
            'detail_currents.*'                    => 'nullable',
            'detail_currents.*.id'                 => 'required|exists:variants,id',
            'detail_currents.*.attribute_id'       => 'required|exists:attributes,id',
            'detail_currents.*.attribute_group_id' => 'required|exists:attribute_groups,id',
        ];
    }

}
