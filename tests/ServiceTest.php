<?php
 
use ContentFarm\DisposableEmail\DisposableEmailService;
 
class ServiceTest extends \PHPUnit_Framework_TestCase {
 
  public function testDisposableEmailCheck()
  {
    $service = new DisposableEmailService;
    $this->assertTrue($service->check('a@a.com'));
  }
 
}
