<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmailHeaderRequest extends EmailConnectionRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), $this->getEmailHeaderRequestRules());
    }

    private function getEmailHeaderRequestRules(){
        return [
            "start" => "required",
            "end" => "required",
        ];
    }

    public function getHeadersData()
    {
        return array_intersect_key($this->all(), array_flip(array_keys($this->getEmailHeaderRequestRules())));
    }

}