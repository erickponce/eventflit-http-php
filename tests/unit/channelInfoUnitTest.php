<?php

class EventflitChannelInfoUnitTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->eventflit = new Eventflit\Eventflit('thisisaauthkey', 'thisisasecret', 1, true);
    }

    /**
     * @expectedException \Eventflit\EventflitException
     */
    public function testTrailingColonChannelThrowsException()
    {
        $this->eventflit->get_channel_info('test_channel:');
    }

    /**
     * @expectedException \Eventflit\EventflitException
     */
    public function testLeadingColonChannelThrowsException()
    {
        $this->eventflit->get_channel_info(':test_channel');
    }

    /**
     * @expectedException \Eventflit\EventflitException
     */
    public function testLeadingColonNLChannelThrowsException()
    {
        $this->eventflit->get_channel_info(':\ntest_channel');
    }

    /**
     * @expectedException \Eventflit\EventflitException
     */
    public function testTrailingColonNLChannelThrowsException()
    {
        $this->eventflit->get_channel_info('test_channel\n:');
    }
}
