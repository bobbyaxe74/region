<?php

namespace App\Http\Requests\EarthRegionControllerRequests;

use App\Http\Requests\BaseRequest;

class EarthRegionSortRequest extends BaseRequest
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
            'country_id' => 'sometimes|required|integer|min:1',
            'state_id' => 'sometimes|required|integer|min:1',
            'city_id' => 'sometimes|required|integer|min:1',
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
            'country_id.sometimes' => 'A country id should be present, else entirely exclude the field',
            'country_id.required' => 'A country id is required',
            'country_id.integer'  => 'Country id characters are not valid, Integer is expected',
            'country_id.min'  => 'Country id characters can not be less than 1',

            'state_id.sometimes' => 'A state id should be present, else entirely exclude the field',
            'state_id.required' => 'A state id is required',
            'state_id.integer'  => 'State id characters are not valid, Integer is expected',
            'state_id.min'  => 'State id characters can not be less than 1',

            'city_id.sometimes' => 'A city id should be present, else entirely exclude the field',
            'city_id.required' => 'A city id is required',
            'city_id.integer'  => 'City id characters are not valid, Integer is expected',
            'city_id.min'  => 'City id characters can not be less than 0',
        ];
    }
}
