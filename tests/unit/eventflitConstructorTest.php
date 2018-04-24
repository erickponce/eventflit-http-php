<?php

class eventflitConstructorTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    public function testDebugCanBeSetViaLegacyParameter()
    {
        $eventflit = new Eventflit\Eventflit('app_key', 'app_secret', 'app_id', true);

        $settings = $eventflit->getSettings();
        $this->assertEquals(true, $settings['debug']);
    }

    public function testHostAndSchemeCanBeSetViaLegacyParameter()
    {
        $scheme = 'http';
        $host = 'test.com';
        $legacy_host = "$scheme://$host";
        $eventflit = new Eventflit\Eventflit('app_key', 'app_secret', 'app_id', false, $legacy_host);

        $settings = $eventflit->getSettings();
        $this->assertEquals($scheme, $settings['scheme']);
        $this->assertEquals($host, $settings['host']);
    }

    public function testLegacyHostParamWithNoSchemeCanBeUsedResultsInHostBeingUsedWithDefaultScheme()
    {
        $host = 'test.com';
        $eventflit = new Eventflit\Eventflit('app_key', 'app_secret', 'app_id', false, $host);

        $settings = $eventflit->getSettings();
        $this->assertEquals($host, $settings['host']);
    }

    public function testSchemeIsSetViaLegacyParameter()
    {
        $host = 'https://test.com';
        $port = 90;
        $eventflit = new Eventflit\Eventflit('app_key', 'app_secret', 'app_id', false, $host, $port);

        $settings = $eventflit->getSettings();
        $this->assertEquals('https', $settings['scheme']);
    }

    public function testPortCanBeSetViaLegacyParameter()
    {
        $host = 'https://test.com';
        $port = 90;
        $eventflit = new Eventflit\Eventflit('app_key', 'app_secret', 'app_id', false, $host, $port);

        $settings = $eventflit->getSettings();
        $this->assertEquals($port, $settings['port']);
    }

    public function testTimeoutCanBeSetViaLegacyParameter()
    {
        $host = 'http://test.com';
        $port = 90;
        $timeout = 90;
        $eventflit = new Eventflit\Eventflit('app_key', 'app_secret', 'app_id', false, $host, $port, $timeout);

        $settings = $eventflit->getSettings();
        $this->assertEquals($timeout, $settings['timeout']);
    }

    public function testEncryptedOptionWillSetHostAndPort()
    {
        $options = array('encrypted' => true);
        $eventflit = new Eventflit\Eventflit('app_key', 'app_secret', 'app_id', $options);

        $settings = $eventflit->getSettings();
        $this->assertEquals('https', $settings['scheme'], 'https');
        $this->assertEquals('service.eventflit.com', $settings['host']);
        $this->assertEquals('443', $settings['port']);
    }

    public function testEncryptedOptionWillBeOverwrittenByHostAndPortOptionsSetHostAndPort()
    {
        $options = array(
            'encrytped' => true,
            'host'      => 'test.com',
            'port'      => '3000',
        );
        $eventflit = new Eventflit\Eventflit('app_key', 'app_secret', 'app_id', $options);

        $settings = $eventflit->getSettings();
        $this->assertEquals('http', $settings['scheme']);
        $this->assertEquals($options['host'], $settings['host']);
        $this->assertEquals($options['port'], $settings['port']);
    }

    public function testSchemeIsStrippedAndIgnoredFromHostInOptions()
    {
        $options = array(
            'host' => 'https://test.com',
        );
        $eventflit = new Eventflit\Eventflit('app_key', 'app_secret', 'app_id', $options);

        $settings = $eventflit->getSettings();
        $this->assertEquals('http', $settings['scheme']);
        $this->assertEquals('test.com', $settings['host']);
    }

    public function testClusterSetsANewHost()
    {
        $options = array(
            'cluster' => 'eu',
        );
        $eventflit = new Eventflit\Eventflit('app_key', 'app_secret', 'app_id', $options);

        $settings = $eventflit->getSettings();
        $this->assertEquals('api-eu.eventflit.com', $settings['host']);
    }

    public function testClusterOptionIsOverriddenByHostIfItExists()
    {
        $options = array(
            'cluster' => 'eu',
            'host'    => 'api.staging.eventflit.com',
        );
        $eventflit = new Eventflit\Eventflit('app_key', 'app_secret', 'app_id', $options);

        $settings = $eventflit->getSettings();
        $this->assertEquals('api.staging.eventflit.com', $settings['host']);
    }

    public function testClusterOptionIsOverriddenByLegacyHostParameter()
    {
        $options = array(
            'cluster' => 'eu',
        );
        $host = 'api.staging.eventflit.com';
        $eventflit = new Eventflit\Eventflit('app_key', 'app_secret', 'app_id', $options, $host);

        $settings = $eventflit->getSettings();
        $this->assertEquals('api.staging.eventflit.com', $settings['host']);
    }

    public function testCurlOptionsCanBeSet()
    {
        $curl_opts = array(CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4);
        $options = array(
            'curl_options' => $curl_opts,
        );
        $eventflit = new Eventflit\Eventflit('app_key', 'app_secret', 'app_id', $options);

        $settings = $eventflit->getSettings();
        $this->assertEquals($curl_opts, $settings['curl_options']);
    }
}
