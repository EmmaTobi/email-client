<?php

namespace App\Services;

interface EmailContract{

    public function connect(array $connectionData) : self ;

    public function setClient($client) : void;

    public function getMsgCount() : int;

    public function getHeaders() : array;

    public function getHeader(int $headerId) : array;

    public function getAllInbox() : array ;

    public function getInbox(array $data) : string ;

	public function close() : void;

}

?>