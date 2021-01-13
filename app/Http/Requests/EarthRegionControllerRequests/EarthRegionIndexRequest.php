<?php

namespace App\Http\Requests\EarthRegionControllerRequests;

use App\Http\Requests\BaseRequest;

class EarthRegionIndexRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'category' => 'required|in:countries,states,cities',
            'properties' => 'nullable|boolean',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'category.required' => 'A category field is required',
            'category.in'  => 'Category field characters are not valid, string of countries, states or cities is expected',

            'properties.boolean'  => 'Properties characters are not valid, Boolean is expected or leave as null',
        ];
    }
}
