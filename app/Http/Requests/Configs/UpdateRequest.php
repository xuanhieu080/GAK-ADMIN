<?php

namespace App\Http\Requests\Configs;

use App\Http\Requests\BaseRequest;

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
            'value' => 'nullable|max:255',
            'file' => 'nullable|mimes:jpg,jpeg,png,bmp,gif,svg,webp,mp4,ogx,oga,ogv,ogg,webm,ico',
        ];
        $config = $this->route('config');
        if (!empty($config) && $config->is_file == 0) {
            $rules['value'] = 'required|max:255';
        }

        return $rules;
    }

}
