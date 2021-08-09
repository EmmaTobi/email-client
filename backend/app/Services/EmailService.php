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
            // throw new EmailException("An Error Occured While Connecting to your mail server. Ensure your credentials are valid and complete");
            throw new EmailException(imap_last_error());
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
				'body'      => imap_fetchbody($this->client, $i, "0"),
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
            $bodyRaw  =  imap_fetchbody($this->client, $msgId , "0");
            return $this->parseHtml(quoted_printable_decode($bodyRaw));
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
        if($this->client)imap_close($this->client);
        \Log::info("Connection Closed Successfully\n");
    }

    /** 
    * Parse Html Tags from mail
    * @param string $bodyEmailMessage
    * @return string
    */
    private function parseHtml(string $bodyEmailMessage) :  string
    {
        $html = $this->parseHtmlFormatZero($bodyEmailMessage);
        if(!$html){
            $html =  $this->parseHtmlFormatOne($bodyEmailMessage);
        }
        return $html ?: $bodyEmailMessage;
    }

    private function parseHtmlFormatZero(string $bodyEmailMessage) :  string
    {
        $result = $this->getStringpart($bodyEmailMessage, "<!DOCTYPE", "</html>");
        if(!$result){
            $result = $this->getStringpart( $bodyEmailMessage, "<body", null );
        }
        return $result ?: $bodyEmailMessage;
    }

    private function parseHtmlFormatOne(string $bodyEmailMessage) :  string
    {
        $result  = $this->getStringpart($bodyEmailMessage, "Content-Type:", "Content-Type:");
        if(!$result){
            $result = $bodyEmailMessage;
        }
        return $result;
    }
    
    /** 
    * Get a part from string
    * @param string $string the string to get part from
    * @param string $startStr the start delimeter
    * @param string $endStr the end delimiter
    * @return string
    */
    private function getStringpart(string $string, string $startStr, string $endStr = null) 
    {
        $startpos=strpos($string, $startStr);
        if(!$startpos){
            return false;
        }else{
            if($endStr){
                $endpos=strpos($string,$endStr,$startpos + 1) + strlen($endStr);
                $endpos=$endpos-$startpos;
            }else{
                $endpos = strlen($string);
            }
            return substr($string,$startpos,$endpos);
        }
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