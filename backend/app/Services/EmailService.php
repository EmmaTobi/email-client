<?php

namespace App\Services;

use App\Exceptions\EmailException;
use \ErrorException;

/**
 * Service class to handle operations related to email
 */
class EmailService implements EmailContract {

    /**
     * @var $
     */
    protected $client;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var array
     */
    protected $inbox = [];

    /**
     * @var int
     */
    protected $msgCount = 0;


    /**
     * @param array $connectionData $connectionData the connection data
     */
    public function connect(array $connectionData) : self
    {
        try{
            $url = "{" . $connectionData["hostname"] . ":" . $connectionData["port"] . "/" . $connectionData["serverType"] . "/". $connectionData["encryption"] ."}";
            $this->setClient(imap_open($url, $connectionData["username"], $connectionData["password"]));
            return $this;
        }catch(ErrorException $e){
            \Log::info($e->getMessage());
            throw new EmailException(implode(", ",imap_errors()));
        }

    }

    /**
     * Set the Connection Object
     * @param  $client the connection object
     * @return void
     */
    public function setClient($client) : void
    {
        $this->client = $client;
    }

    /**
     * Set the Msg Count
     * @return int
     */
    public function getMsgCount() : int
    {
        $this->msgCount =   $this->msgCount == 0 ? imap_num_msg($this->client) : $this->msgCount ;
        return  $this->msgCount;
    }

    /**
     * Get All Headers
     * @return array
     */
    public function getHeaders() : array
    {
        if(count($this->headers)){
            return $this->headers;
        }else{
            $count = $this->getMsgCount();
            for($i = $count; $i >= 1; $i--) {
               $header_info_object = imap_headerinfo($this->client,  $i);
               $this->headers[$i] = ["date" => $header_info_object->date,
                                     "subject"=>$header_info_object->subject,
                                     "fromAddress" => $header_info_object->fromaddress
                                    ];
            }
            return $this->headers;
        }
    }

    /**
     * Get An Header By Id
     * @param int $headerId the header id
     * @return array
     */
    public function getHeader(int $headerId) : array
    {
        $header_info_object = imap_header($this->client,  $headerId);
        return[
                "id" => $headerId,
                "date" => $header_info_object->date,
                "subject"=> imap_utf8($header_info_object->subject),
                "fromAddress" => $header_info_object->fromaddress
                ];
    }

    /**
     * Get All Inbox
     * @return array
     */
    public function getAllInbox() : array
    {
        $in = array();
        $count = $this->getMsgCount();
		for($i = $count; $i >= 1; $i--) {
			$msg = array(
				'index'     => $i,
				'header'    => imap_headerinfo($this->client, $i),
				'body'      => $this->getBody($i, $this->client),
				'structure' => imap_fetchstructure($this->client, $i)
            );
            $in[$i] = $msg;
		}
        $this->inbox = $in;
        return $this->inbox;
    }

    /**
     * Get An Inbox By Id
     * @param array $data the array containing msgId
     * @return array
     */
    public function getInbox(array $data) : string
    {
        $msgId = (int)$data["msgId"];
        $count = $this->getMsgCount();
        if( $count &&  ($msgId <= $count )){
            return $this->getBody($msgId, $this->client);
        }
        return false;
    }

    /**
     * Get Paginated Headers
     * @param array $data the array containing start and end boundaries
     * @return array
     */
    public function getPaginatedHeaders(array $data) : array
    {
        $headers = [];
        $start = $data["start"];
        $end = $data["end"];
        for(; $start <= $end; $start++ ){
            $headers[] = $this->getHeader($start);
        }
        return $headers;
    }

    /** 
    * Close the connection
    * @return void
    */
    public function close() : void
    {
		$this->inbox = array();
        $this->msgCount = 0;
        imap_errors();
        if($this->client)imap_close($this->client);
        \Log::info("Connection Closed Successfully\n");
    }

    /**
     * Get Email Body
     * @param $uid int the email id
     * @param $imap client the client connection object
     * return string
     */
    protected function getBody($uid, $imap) : string {
        $body = $this->get_part($imap, $uid, "TEXT/HTML");
        // if HTML body is empty, try getting text body
        if ($body == "") {
            $body = $this->get_part($imap, $uid, "TEXT/PLAIN");
        }
        return $body;
    }  
    
    /**
     * Get Email Body
     * @param $uid int the email id
     * @param $imap client the client connection object
     * @param $mimetype string the required email mime type
     * @param $structure boolean the email structure
     * @param $partNumber boolean the email part number
     * return string
     */
    private function get_part($imap, $uid, $mimetype, $structure = false, $partNumber = false) {
        if (!$structure) {
           $structure = imap_fetchstructure($imap, $uid, FT_UID);
        }
        if ($structure) {
            if ($mimetype == $this->get_mime_type($structure)) {
                if (!$partNumber) {
                    $partNumber = 0;
                }
                $text = imap_fetchbody($imap, $uid, $partNumber, FT_UID);
                
                switch ($structure->encoding) {
                    case 3: return imap_base64($text);
                    case 4: return imap_qprint($text);
                    default: return $text;
                }
            }
            // multipart
            if ($structure->type == 1) {
                foreach ($structure->parts as $index => $subStruct) {
                    $prefix = "";
                    if ($partNumber) {
                        $prefix = $partNumber . ".";
                    }
                    $data = $this->get_part($imap, $uid, $mimetype, $subStruct,
                        $prefix. ($index + 1));
                    if ($data) {
                        return $data;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Get Email Mime type
     * @param $structure boolean the email structure
     * return string
     */
    private function get_mime_type($structure) : string {
        $primaryMimetype = array("TEXT", "MULTIPART", "MESSAGE", "APPLICATION",
            "AUDIO", "IMAGE", "VIDEO", "OTHER");
    
        if ($structure->subtype) {
           return $primaryMimetype[(int)$structure->type] . "/" . $structure->subtype;
        }
        return "TEXT/PLAIN";
    }  
    
    /** 
    * Get a part from string
    * @param array $extractDataFromHeader an header to extract data [date, subject, fromaddress]from
    * @return string
    */
    private function extractDataFromHeader(array $header)
    {
        return array_intersect_key($header, array_flip(array('Date', 'Subject', 'fromaddress')));
    }

}

?>