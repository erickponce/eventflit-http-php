<?php

namespace Eventflit;

class EventflitInstance
{
    private static $instance = null;
    private static $app_id = '';
    private static $secret = '';
    private static $api_key = '';

    public static function get_eventflit()
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        self::$instance = new Eventflit(
            self::$api_key,
            self::$secret,
            self::$app_id
        );

        return self::$instance;
    }
}
