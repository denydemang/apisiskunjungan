<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator as ContractsValidationValidator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SaveKunjunganRequest extends FormRequest
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
            "user_id" => 'required',
            "project_id" => 'required',
            "tgl_knj" => 'required',
            "foto_knj" => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            "lokasi_knj" => 'required',
            "latlong_knj" => 'required',
            "pekerjaan_knj" => 'required',
            "kategori_knj" => 'required',
            "sumber_knj" => 'required',
            "hasil_knj" => 'required',
            "kontak_knj" => 'required',
        ];
    }
    
    protected function failedValidation(ContractsValidationValidator $validator){
        throw new HttpResponseException(response(
            ["errors" => $validator->getMessageBag()],
            
            400));
    }
}
