<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Access\AuthorizationException as AuthorizationException;

class BaseRequest
{
    public function __construct($request) {

        if (!$this->authorize()) {
            throw new AuthorizationException();
        }

        Validator::make($request->all(), $this->rules(), $this->messages())->validate();
    }

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
            //
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
           //
        ];
    }
}
