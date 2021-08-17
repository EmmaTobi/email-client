<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class EmailConnectionRequest extends FormRequest
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
            "hostname" => "required",
            "serverType" => "required", 
            "port" => "required", 
            "encryption" => "required", 
            "username" => "required", 
            "password" => "required"
        ];
    }

    public function getConnectionData()
    {
       return $this->all();
    }


    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors_as_string = null;
        $errors = (new ValidationException($validator))->errors();

        foreach ($errors as $error)
            $errors_as_string .= $error[0].', ';


        throw new HttpResponseException(
            response()->json(array(
                'success' => false,
                'message' => $errors_as_string
            ), 500)
        );
    }

}