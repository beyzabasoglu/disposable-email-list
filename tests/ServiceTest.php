<?php

use ContentFarm\DisposableEmail\DisposableEmailService;

class ServiceTest extends \PHPUnit_Framework_TestCase
{

    public function testDisposableEmailCheck()
    {
        $service = new DisposableEmailService;
        $service->mail = 'a@yuurok.com';
        $this->assertTrue($service->isDisposableEmail());
    }

    public function testGmailCheck()
    {
        $service = new DisposableEmailService;
        $service->mail = 'a@gmail.com';
        $this->assertFalse($service->isDisposableEmail());
    }

}
