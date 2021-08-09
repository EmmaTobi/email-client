<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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

}