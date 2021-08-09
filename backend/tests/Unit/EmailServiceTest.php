<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\EmailService;

class EmailServiceTest extends TestCase
{

    protected $emailService;

    public function setup():void
    {
        parent::setUp();
        $this->emailService = app()->make(EmailService::class);
    }

    /**
     * Test Connect functionality on email service.
     *
     * @return void
     */
    public function testConnect()
    {
        $this->assertNotNull($this->emailService->connect($this->getConnectionData()));
    }

    protected function getConnectionData() :  array
    {
        return $connectionData = [ "hostname" => "pop3.mailtrap.io", "port" => "9950",  "serverType" => "pop3",
                                    "encryption" => "notls", "username" => "98c8b0b0257871", "password" => "2c5228d7f7c468"
                                    ];
    }

}
