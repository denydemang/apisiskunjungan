<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator as ContractsValidationValidator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as ValidationValidator;

class UserRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool
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
            "email" => 'email|required|unique:users',
            "name" => 'required',
            "divisi" => 'required',
            "jabatan" => 'required',
            "jenis_user" => 'required',
            "emp_id" => 'required',
            "password" => 'required',
        ];
    }

    protected function failedValidation(ContractsValidationValidator $validator){
        throw new HttpResponseException(response(
            ["errors" => $validator->getMessageBag()],
            
            400));
    }
}
