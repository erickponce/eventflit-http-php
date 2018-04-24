<?php

    class EventflitNotificationsUnitTest extends PHPUnit_Framework_TestCase
    {
        protected function setUp()
        {
            $this->eventflit = new Eventflit\Eventflit('thisisaauthkey', 'thisisasecret', 1);
        }

        /**
         * @expectedException \Eventflit\EventflitException
         */
        public function testInvalidEmptyInterests()
        {
            $this->eventflit->notify(array(), array('foo' => 'bar'));
        }
    }
