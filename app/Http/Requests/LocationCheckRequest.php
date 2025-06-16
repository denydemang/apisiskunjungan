<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator as ContractsValidationValidator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LocationCheckRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'image_base64' => 'nullable|string',
            'root_jailbreak' => 'required|boolean'
        ];
    }

     protected function failedValidation(ContractsValidationValidator $validator){
        throw new HttpResponseException(response(
            ["errors" => $validator->getMessageBag()],
            
            400));
    }
}