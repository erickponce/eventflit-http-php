<?php

class EventflitTriggerUnitTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->eventflit = new Eventflit\Eventflit('thisisaauthkey', 'thisisasecret', 1, true);
        $this->eventName = 'test_event';
        $this->data = array();
    }

    /**
     * @expectedException \Eventflit\EventflitException
     */
    public function testTrailingColonChannelThrowsException()
    {
        $this->eventflit->trigger('test_channel:', $this->eventName, $this->data);
    }

    /**
     * @expectedException \Eventflit\EventflitException
     */
    public function testLeadingColonChannelThrowsException()
    {
        $this->eventflit->trigger(':test_channel', $this->eventName, $this->data);
    }

    /**
     * @expectedException \Eventflit\EventflitException
     */
    public function testLeadingColonNLChannelThrowsException()
    {
        $this->eventflit->trigger(':\ntest_channel', $this->eventName, $this->data);
    }

    /**
     * @expectedException \Eventflit\EventflitException
     */
    public function testTrailingColonNLChannelThrowsException()
    {
        $this->eventflit->trigger('test_channel\n:', $this->eventName, $this->data);
    }

    /**
     * @expectedException \Eventflit\EventflitException
     */
    public function testChannelArrayThrowsException()
    {
        $this->eventflit->trigger(array('this_one_is_okay', 'test_channel\n:'), $this->eventName, $this->data);
    }

    /**
     * @expectedException \Eventflit\EventflitException
     */
    public function testTrailingColonSocketIDThrowsException()
    {
        $this->eventflit->trigger('test_channel:', $this->eventName, $this->data, '1.1:');
    }

    /**
     * @expectedException \Eventflit\EventflitException
     */
    public function testLeadingColonSocketIDThrowsException()
    {
        $this->eventflit->trigger('test_channel:', $this->eventName, $this->data, ':1.1');
    }

    /**
     * @expectedException \Eventflit\EventflitException
     */
    public function testLeadingColonNLSocketIDThrowsException()
    {
        $this->eventflit->trigger('test_channel:', $this->eventName, $this->data, ':\n1.1');
    }

    /**
     * @expectedException \Eventflit\EventflitException
     */
    public function testTrailingColonNLSocketIDThrowsException()
    {
        $this->eventflit->trigger('test_channel:', $this->eventName, $this->data, '1.1\n:');
    }

    public function testNullSocketID()
    {
        // Check this does not throw an exception
        $this->eventflit->trigger('test_channel', $this->eventName, $this->data, null);
    }

    /**
     * @expectedException \Eventflit\EventflitException
     */
    public function testFalseSocketIDThrowsException()
    {
        $this->eventflit->trigger('test_channel', $this->eventName, $this->data, false);
    }

    /**
     * @expectedException \Eventflit\EventflitException
     */
    public function testEmptyStrSocketIDThrowsException()
    {
        $this->eventflit->trigger('test_channel', $this->eventName, $this->data, '');
    }
}
