<?php

class EventflitSocketAuthTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->eventflit = new Eventflit\Eventflit('thisisaauthkey', 'thisisasecret', 1, true);
    }

    public function testObjectConstruct()
    {
        $this->assertNotNull($this->eventflit, 'Created new Eventflit\Eventflit object');
    }

    public function testSocketAuthKey()
    {
        $socket_auth = $this->eventflit->socket_auth('testing_eventflit-php', '1.1');
        $this->assertEquals($socket_auth,
        '{"auth":"thisisaauthkey:751ccc12aeaa79d46f7c199bced5fa47527d3480b51fe61a0bd10438241bd52d"}',
        'Socket auth key valid');
    }

    public function testComplexSocketAuthKey()
    {
        $socket_auth = $this->eventflit->socket_auth('-azAZ9_=@,.;', '45055.28877557');
        $this->assertEquals($socket_auth,
        '{"auth":"thisisaauthkey:d1c20ad7684c172271f92c108e11b45aef07499b005796ae1ec5beb924f361c4"}',
        'Socket auth key valid');
    }

    /**
     * @expectedException \Eventflit\EventflitException
     */
    public function testTrailingColonSocketIDThrowsException()
    {
        $this->eventflit->socket_auth('testing_eventflit-php', '1.1:');
    }

    /**
     * @expectedException \Eventflit\EventflitException
     */
    public function testLeadingColonSocketIDThrowsException()
    {
        $this->eventflit->socket_auth('testing_eventflit-php', ':1.1');
    }

    /**
     * @expectedException \Eventflit\EventflitException
     */
    public function testLeadingColonNLSocketIDThrowsException()
    {
        $this->eventflit->socket_auth('testing_eventflit-php', ':\n1.1');
    }

    /**
     * @expectedException \Eventflit\EventflitException
     */
    public function testTrailingColonNLSocketIDThrowsException()
    {
        $this->eventflit->socket_auth('testing_eventflit-php', '1.1\n:');
    }

    /**
     * @expectedException \Eventflit\EventflitException
     */
    public function testTrailingColonChannelThrowsException()
    {
        $this->eventflit->socket_auth('test_channel:', '1.1');
    }

    /**
     * @expectedException \Eventflit\EventflitException
     */
    public function testLeadingColonChannelThrowsException()
    {
        $this->eventflit->socket_auth(':test_channel', '1.1');
    }

    /**
     * @expectedException \Eventflit\EventflitException
     */
    public function testLeadingColonNLChannelThrowsException()
    {
        $this->eventflit->socket_auth(':\ntest_channel', '1.1');
    }

    /**
     * @expectedException \Eventflit\EventflitException
     */
    public function testTrailingColonNLChannelThrowsException()
    {
        $this->eventflit->socket_auth('test_channel\n:', '1.1');
    }
}
