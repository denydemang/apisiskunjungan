<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator as ContractsValidationValidator;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserLoginRequest extends FormRequest
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
     * @return array<string, mixed>
     */
   public function rules()
    {
        return [
            "email" => 'required',
            "password" => 'required',
        ];
        
    }
    protected function failedValidation(ContractsValidationValidator $validator){
        throw new HttpResponseException(response(
            ["errors" => $validator->getMessageBag()],
            
            400));
    }
}
