<?php

use PHPUnit\Framework\TestCaseTest;
use Pollus\FakeSession\FakeSession;
use PHPUnit\Framework\Error\Notice;
use PHPUnit\Framework\Error\Warning;

class FakeSessionTest extends \PHPUnit\Framework\TestCase
{
    public function testSessionStart()
    {
        $session = new FakeSession();
        $this->assertSame(PHP_SESSION_NONE, $session->status());
        $session->start();
        $this->assertSame(PHP_SESSION_ACTIVE, $session->status());
        $session->destroy();
        $this->assertSame(PHP_SESSION_NONE, $session->status());
    }
    
    public function testSessionIdRandom()
    {
        $session = new FakeSession();
        $this->assertSame(null, $session->id);
        $session->start();
        $id = $session->id();
        $this->assertNotSame(null, $id);
        $this->assertSame($id, $session->id);
    }
    
    public function testSessionIdFixed()
    {
        $session = new FakeSession();
        $session->id("123456");
        $session->start();
        $this->assertSame("123456", $session->id());
        $session->regenerateId();
        $this->assertNotSame("123456", $session->id());
    }
    
    public function testSessionSetAndGet()
    {
        $session = new FakeSession();
        $session->start();
        $session->set("age", 10);
        $this->assertSame(10, $session->get("age", 10));
    }
    
    public function testSessionStartTwiceTriggersNotice()
    {
        $this->expectException(Notice::class);
        $session = new FakeSession();
        $session->start();
        $session->start();
    }
    
    public function testSessionDestroyWithoutStartTriggersWarning()
    {
        $this->expectException(Warning::class);
        $session = new FakeSession();
        $session->destroy();
    }
    
    public function testSessionStartedChangeNameTriggersWarning()
    {
        $this->expectException(Warning::class);
        $session = new FakeSession();
        $session->start();
        $session->name("test");
    }
}
