<?php

namespace App\Exceptions;

use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Client;
use \Exception;

/**
 * Custom Exception wrapper to handle exceptions from application 
 */
class EmailException extends Exception{

    public function __construct($message){
        parent::__construct($message);
    }
}

?>