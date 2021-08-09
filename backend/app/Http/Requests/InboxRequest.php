<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InboxRequest extends EmailConnectionRequest
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
        return array_merge(parent::rules(), $this->getInboxRequestRules());
    }

    private function getInboxRequestRules(){
        return [
            "msgId" => "required",
        ];
    }

    public function getInboxData()
    {
        return array_intersect_key($this->all(), array_flip(array_keys($this->getInboxRequestRules())));
    }
}