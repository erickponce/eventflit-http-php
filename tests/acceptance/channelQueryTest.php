<?php

class EventflitChannelQueryTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->eventflit = new Eventflit\Eventflit(EVENTFLITAPP_AUTHKEY, EVENTFLITAPP_SECRET, EVENTFLITAPP_APPID, true, EVENTFLITAPP_HOST);
        $this->eventflit->setLogger(new TestLogger());
    }

    public function testChannelInfo()
    {
        $response = $this->eventflit->get_channel_info('channel-test');

        //print_r( $response );

        $this->assertObjectHasAttribute('occupied', $response, 'class has occupied attribute');
    }

    public function testChannelList()
    {
        $result = $this->eventflit->get_channels();
        $channels = $result->channels;

        // print_r( $channels );

        foreach ($channels as $channel_name => $channel_info) {
            echo  "channel_name: $channel_name\n";
            echo  'channel_info: ';
            print_r($channel_info);
            echo  "\n\n";
        }

        $this->assertTrue(is_array($channels), 'channels is an array');
    }

    public function testFilterByPrefixNoChannels()
    {
        $options = array(
            'filter_by_prefix' => '__fish',
        );
        $result = $this->eventflit->get_channels($options);

        // print_r( $result );

        $channels = $result->channels;

        // print_r( $channels );

        $this->assertTrue(is_array($channels), 'channels is an array');
        $this->assertEquals(0, count($channels), 'should be an empty array');
    }

    public function testFilterByPrefixOneChannel()
    {
        $options = array(
            'filter_by_prefix' => 'my-',
        );
        $result = $this->eventflit->get_channels($options);

        // print_r( $result );

        $channels = $result->channels;

        // print_r( $channels );

        $this->assertEquals(1, count($channels), 'channels have a single test-channel present. For this test to pass you must have the "Getting Started" page open on the dashboard for the app you are testing against');
    }

    public function test_providing_info_parameter_with_prefix_query_fails_for_public_channel()
    {
        $options = array(
            'filter_by_prefix' => 'test_',
            'info'             => 'user_count',
        );
        $result = $this->eventflit->get_channels($options);

        $this->assertFalse($result, 'query should fail');
    }

    public function test_channel_list_using_generic_get()
    {
        $response = $this->eventflit->get('/channels');

        $this->assertEquals($response['status'], 200);

        $result = $response['result'];

        $channels = $result['channels'];

        $this->assertEquals(1, count($channels), 'channels have a single test-channel present. For this test to pass you must have the "Getting Started" page open on the dashboard for the app you are testing against');

        $test_channel = $channels['test_channel'];

        $this->assertEquals(0, count($test_channel));
    }

    public function test_channel_list_using_generic_get_and_prefix_param()
    {
        $response = $this->eventflit->get('/channels', array('filter_by_prefix' => 'my-'));

        $this->assertEquals($response['status'], 200);

        $result = $response['result'];

        $channels = $result['channels'];

        $this->assertEquals(1, count($channels), 'channels have a single test-channel present. For this test to pass you must have the "Getting Started" page open on the dashboard for the app you are testing against');

        $test_channel = $channels['test_channel'];

        $this->assertEquals(0, count($test_channel));
    }

    public function test_single_channel_info_using_generic_get()
    {
        $response = $this->eventflit->get('/channels/channel-test');

        $this->assertEquals($response['status'], 200);

        $result = $response['result'];

        $this->assertArrayHasKey('occupied', $result, 'class has occupied attribute');
    }
}
