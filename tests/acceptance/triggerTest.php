<?php

class EventflitPushTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (EVENTFLITAPP_AUTHKEY === '' || EVENTFLITAPP_SECRET === '' || EVENTFLITAPP_APPID === '') {
            $this->markTestSkipped('Please set the
            EVENTFLITAPP_AUTHKEY, EVENTFLITAPP_SECRET and
            EVENTFLITAPP_APPID keys.');
        } else {
            $this->eventflit = new Eventflit\Eventflit(EVENTFLITAPP_AUTHKEY, EVENTFLITAPP_SECRET, EVENTFLITAPP_APPID, true, EVENTFLITAPP_HOST);
            $this->eventflit->setLogger(new TestLogger());
        }
    }

    public function testObjectConstruct()
    {
        $this->assertNotNull($this->eventflit, 'Created new Eventflit\Eventflit object');
    }

    public function testStringPush()
    {
        $string_trigger = $this->eventflit->trigger('test_channel', 'my_event', 'Test string');
        $this->assertTrue($string_trigger, 'Trigger with string payload');
    }

    public function testArrayPush()
    {
        $structure_trigger = $this->eventflit->trigger('test_channel', 'my_event', array('test' => 1));
        $this->assertTrue($structure_trigger, 'Trigger with structured payload');
    }

    public function testEncryptedPush()
    {
        $options = array(
            'encrypted' => true,
            'host'      => EVENTFLITAPP_HOST,
        );
        $eventflit = new Eventflit\Eventflit(EVENTFLITAPP_AUTHKEY, EVENTFLITAPP_SECRET, EVENTFLITAPP_APPID, $options);
        $eventflit->setLogger(new TestLogger());

        $structure_trigger = $eventflit->trigger('test_channel', 'my_event', array('encrypted' => 1));
        $this->assertTrue($structure_trigger, 'Trigger with over encrypted connection');
    }

    public function testSendingOver10kBMessageReturns413()
    {
        $data = str_pad('', 11 * 1024, 'a');
        echo  'sending data of size: '.mb_strlen($data, '8bit');
        $response = $this->eventflit->trigger('test_channel', 'my_event', $data, null, true);
        $this->assertEquals(413, $response['status'], '413 HTTP status response expected');
    }

    /**
     * @expectedException \Eventflit\EventflitException
     */
    public function test_triggering_event_on_over_100_channels_throws_exception()
    {
        $channels = array();
        while (count($channels) <= 101) {
            $channels[] = ('channel-'.count($channels));
        }
        $data = array('event_name' => 'event_data');
        $response = $this->eventflit->trigger($channels, 'my_event', $data);
    }

    public function test_triggering_event_on_multiple_channels()
    {
        $data = array('event_name' => 'event_data');
        $channels = array('test_channel_1', 'test_channel_2');
        $response = $this->eventflit->trigger($channels, 'my_event', $data);

        $this->assertTrue($response);
    }
}
