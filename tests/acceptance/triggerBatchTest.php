<?php

class EventflitBatchPushTest extends PHPUnit_Framework_TestCase
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

    public function testSimplePush()
    {
        $batch = array();
        $batch[] = array('channel' => 'test_channel', 'name' => 'my_event', 'data' => array('my' => 'data'));
        $string_trigger = $this->eventflit->triggerBatch($batch);
        $this->assertTrue($string_trigger, 'Trigger with string payload');
    }

    public function testEncryptedPush()
    {
        $options = array(
            'encrypted' => true,
            'host'      => EVENTFLITAPP_HOST,
        );
        $eventflit = new Eventflit\Eventflit(EVENTFLITAPP_AUTHKEY, EVENTFLITAPP_SECRET, EVENTFLITAPP_APPID, $options);
        $eventflit->setLogger(new TestLogger());

        $batch = array();
        $batch[] = array('channel' => 'test_channel', 'name' => 'my_event', 'data' => array('my' => 'data'));
        $string_trigger = $this->eventflit->triggerBatch($batch);
        $this->assertTrue($string_trigger, 'Trigger with string payload');
    }

    public function testSendingOver10kBMessageReturns413()
    {
        $data = str_pad('', 11 * 1024, 'a');
        $batch = array();
        $batch[] = array('channel' => 'test_channel', 'name' => 'my_event', 'data' => $data);
        $response = $this->eventflit->triggerBatch($batch, true, true);
        $this->assertContains('content of this event', $response['body']);
        $this->assertEquals(413, $response['status'], '413 HTTP status response expected');
    }

    public function testSendingOver10messagesReturns400()
    {
        $batch = array();
        foreach (range(1, 11) as $i) {
            $batch[] = array('channel' => 'test_channel', 'name' => 'my_event', 'data' => array('index' => $i));
        }
        $response = $this->eventflit->triggerBatch($batch, true, false);
        $this->assertContains('Batch too large', $response['body']);
        $this->assertEquals(400, $response['status'], '400 HTTP status response expected');
    }
}
