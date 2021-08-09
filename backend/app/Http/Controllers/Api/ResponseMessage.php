<?php

namespace App\Http\Controllers\Api;

class ResponseMessage
{
    private $data;

    private $status;

    private $message;

    public function __construct($data, $status, $message)
    {
        $this->data = $data;
        $this->status = $status;
        $this->message = $message;
    }

    public function toArray()
    {
       return [
            "data" => $this->getData(),
            "status" => $this->getStatus(),
            "message" => $this->getMessage(),
       ];
    }

    public function getData()
    {
        return $this->data;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getMessage()
    {
        return $this->message;
    }
}
