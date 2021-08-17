<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Exceptions\EmailException;
use App\Services\EmailContract;
use App\Http\Requests\EmailHeaderRequest;
use App\Http\Requests\EmailConnectionRequest;
use App\Http\Requests\InboxRequest;
use App\Http\Controllers\Controller;


/**
 * Controller handling email related requests
 */
class EmailClientController extends Controller
{

    /**
     * @var EmailContract
     */
    protected $emailService;

    /**
     * Constructor
     * @param EmailContract $emailService the email service
     */
    public function __construct(EmailContract $emailService)
    {
        $this->emailService  = $emailService;
    }

    /**
     * @param EmailHeaderRequest $request request wrapper related to email header
     * @return  Illuminate\Http\Response
     */
    public function indexEmailHeadersPaginated(EmailHeaderRequest $request){
        try {
            $headers = $this->emailService->connect($request->getConnectionData())->getPaginatedHeaders($request->getHeadersData());
            return response()->json((new ResponseMessage($headers,"success","Get Paginated Headers Successful"))->toArray());
        } catch (EmailException $e) {
            \Log::error($e->getMessage());
            return response()->json((new ResponseMessage(null,"error",$e->getMessage()))->toArray(), 500);
        }finally{
            $this->emailService->close();
        }
    }

    /**
     * @param EmailHeaderRequest $request request wrapper related to email header
     * @return  Illuminate\Http\Response
     */
    public function getInbox(InboxRequest $request){
        try{
            $msg = $this->emailService->connect($request->getConnectionData())->getInbox($request->getInboxData());
            return response()->json((new ResponseMessage($msg,"success","Get Inbox Successful"))->toArray());
        } catch (EmailException $e) {
            \Log::error($e->getMessage());
            return response()->json((new ResponseMessage(null,"error",$e->getMessage()))->toArray(), 500);
        }finally{
            $this->emailService->close();
        }
    }

    /**
     * @param EmailHeaderRequest $request request wrapper related to email connection
     * @return  Illuminate\Http\Response
     */
    public function connectToMailBox(EmailConnectionRequest $request){
        try{
            $msgCount = $this->emailService->connect($request->getConnectionData())->getMsgCount();
            return response()->json((new ResponseMessage(["msgCount" => $msgCount],"success","Connection Successful"))->toArray());
        } catch (EmailException $e) {
            \Log::error($e->getMessage());
            return response()->json((new ResponseMessage(null,"error",$e->getMessage()))->toArray(), 500);
        }finally{
            $this->emailService->close();
        }
    }

}
