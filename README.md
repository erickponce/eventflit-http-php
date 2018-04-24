# Eventflit PHP Library

PHP library for interacting with the Eventflit HTTP API.

Register at <https://eventflit.com> and use the application credentials within your app as shown below.

## Installation

You can get the Eventflit PHP library via a composer package called `eventflit-http-php`. See <https://packagist.org/packages/eventflit/eventflit-http-php>

```bash
$ composer require eventflit/eventflit-http-php
```

Or add to `composer.json`:

```json
"require": {
    "eventflit/eventflit-http-php": "^0.1"
}
```

and then run `composer update`.

Or you can clone or download the library files.

**We recommend you [use composer](http://getcomposer.org/).**

This library depends on PHP modules for cURL and JSON. See [cURL module installation instructions](http://php.net/manual/en/curl.installation.php) and [JSON module installation instructions](http://php.net/manual/en/json.installation.php).


## Eventflit constructor

Use the credentials from your Eventflit application to create a new `Eventflit\Eventflit` instance.

```php
$app_id = 'YOUR_APP_ID';
$app_key = 'YOUR_APP_KEY';
$app_secret = 'YOUR_APP_SECRET';
$app_cluster = 'YOUR_APP_CLUSTER';

$eventflit = new Eventflit\Eventflit( $app_key, $app_secret, $app_id, array('cluster' => $app_cluster) );
```

The fourth parameter is an `$options` array. The additional options are:

* `scheme` - e.g. http or https
* `host` - the host e.g. service.eventflit.com. No trailing forward slash.
* `port` - the http port
* `timeout` - the HTTP timeout
* `encrypted` - quick option to use scheme of https and port 443.
* `cluster` - specify the cluster where the application is running from.
* `curl_options` - array with custom curl commands

For example, by default calls will be made over a non-encrypted connection. To change this to make calls over HTTPS use:

```php
$eventflit = new Eventflit\Eventflit( $app_key, $app_secret, $app_id, array( 'cluster' => $app_cluster, 'encrypted' => true ) );
```

For example, if you want to set custom curl options, use this:
```php
$eventflit = new Eventflit\Eventflit( $app_key, $app_secret, $app_id, array( 'cluster' => $app_cluster, 'encrypted' => true, 'curl_options' => array( CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4 ) ) );
```

*Note: The `host` option overrides the `cluster` option!*

## Publishing/Triggering events

To trigger an event on one or more channels use the `trigger` function.

### A single channel

```php
$eventflit->trigger( 'my-channel', 'my_event', 'hello world' );
```

### Multiple channels

```php
$eventflit->trigger( [ 'channel-1', 'channel-2' ], 'my_event', 'hello world' );
```

### Batches

It's also possible to send multiple events with a single API call (max 10
events per call on multi-tenant clusters):

```php
$batch = array();
$batch[] = array('channel' => 'my-channel', 'name' => 'my_event', 'data' => array('hello' => 'world'));
$batch[] = array('channel' => 'my-channel', 'name' => 'my_event', 'data' => array('myname' => 'bob'));
$eventflit->triggerBatch($batch);
```

### Arrays

Objects are automatically converted to JSON format:

```php
$array['name'] = 'joe';
$array['message_count'] = 23;

$eventflit->trigger('my_channel', 'my_event', $array);
```

The output of this will be:

```json
"{'name': 'joe', 'message_count': 23}"
```

### Socket id

In order to avoid duplicates you can optionally specify the sender's socket id while triggering an event ([https://eventflit.com/docs/duplicates](http://eventflitapp.com/docs/duplicates)):

```php
$eventflit->trigger('my-channel','event','data','socket_id');
```

### JSON format

If your data is already encoded in JSON format, you can avoid a second encoding step by setting the sixth argument true, like so:

```php
$eventflit->trigger('my-channel', 'event', 'data', null, false, true)
```

## Authenticating Private channels

To authorise your users to access private channels on Eventflit, you can use the socket_auth function:

```php
$eventflit->socket_auth('private-my-channel','socket_id');
```

## Authenticating Presence channels

Using presence channels is similar to private channels, but you can specify extra data to identify that particular user:

```php
$eventflit->presence_auth('presence-my-channel','socket_id', 'user_id', 'user_info');
```

### Presence example

First set this variable in your JS app:

```php
Eventflit.channel_auth_endpoint = '/presence_auth.php';
```

Next, create the following in presence_auth.php:

```php
<?php
if (isset($_SESSION['user_id'])) {
  $stmt = $pdo->prepare("SELECT * FROM `users` WHERE id = :id");
  $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
  $stmt->execute();
  $user = $stmt->fetch();
} else {
  die('aaargh, no-one is logged in');
}

header('Content-Type: application/json');

$eventflit = new Eventflit\Eventflit($key, $secret, $app_id);
$presence_data = array('name' => $user['name']);

echo $eventflit->presence_auth($_POST['channel_name'], $_POST['socket_id'], $user['id'], $presence_data);
```

Note: this assumes that you store your users in a table called `users` and that those users have a `name` column. It also assumes that you have a login mechanism that stores the `user_id` of the logged in user in the session.

## Application State Queries

### Get information about a channel

```php
$eventflit->get_channel_info( $name );
```

It's also possible to get information about a channel from the Eventflit REST API.

```php
$info = $eventflit->get_channel_info('channel-name');
$channel_occupied = $info->occupied;
```

For [presence channels](https://eventflit.com/docs/presence_channels) you can also query the number of distinct users currently subscribed to this channel (a single user may be subscribed many times, but will only count as one):

```php
$info = $eventflit->get_channel_info('presence-channel-name', array('info' => 'user_count'));
$user_count = $info->user_count;
```

If you have enabled the ability to query the `subscription_count` (the number of connections currently subscribed to this channel) then you can query this value as follows:

```php
$info = $eventflit->get_channel_info('presence-channel-name', array('info' => 'subscription_count'));
$subscription_count = $info->subscription_count;
```

### Get a list of application channels

```php
$eventflit->get_channels()
```

It's also possible to get a list of channels for an application from the Eventflit REST API.

```php
$result = $eventflit->get_channels();
$channel_count = count($result->channels); // $channels is an Array
```

### Get a filtered list of application channels

```php
$eventflit->get_channels( array( 'filter_by_prefix' => 'some_filter' ) )
```

It's also possible to get a list of channels based on their name prefix. To do this you need to supply an $options parameter to the call. In the following example the call will return a list of all channels with a 'presence-' prefix. This is idea for fetching a list of all presence channels.

```php
$results = $eventflit->get_channels( array( 'filter_by_prefix' => 'presence-') );
$channel_count = count($result->channels); // $channels is an Array
```

This can also be achieved using the generic `eventflit->get` function:

```php
$eventflit->get( '/channels', array( 'filter_by_prefix' => 'presence-' ) );
```

### Get user information from a presence channel

```php
$response = $eventflit->get( '/channels/presence-channel-name/users' )
```

The `$response` is in the format:

```php
Array
(
    [body] => {"users":[{"id":"a_user_id"}]}
    [status] => 200
    [result] => Array
        (
            [users] => Array
                (
                    [0] => Array
                        (
                            [id] => a_user_id
                        )
                    /* Additional users */
                )
        )
)
```

### Generic get function

```php
$eventflit->get( $path, $params );
```

Used to make `GET` queries against the Eventflit REST API. Handles authentication.

Response is an associative array with a `result` index. The contents of this index is dependent on the REST method that was called. However, a `status` property to allow the HTTP status code is always present and a `result` property will be set if the status code indicates a successful call to the API.

```php
$response = $eventflit->get( '/channels' );
$http_status_code = $response[ 'status' ];
$result = $response[ 'result' ];
```

## Push Notifications (BETA)

Eventflit now allows sending native notifications to iOS and Android devices. Check out the [documentation](https://docs.eventflit.com/push_notifications) for information on how to set up push notifications on Android and iOS. There is no additional setup required to use it with this library. It works out of the box with the same Eventflit instance. All you need are the same eventflit credentials.

The native notifications API is hosted at `push.eventflit.com` and only listens on HTTPS.
If you wish to provide a different host you can do:

```php
$eventflit = new Eventflit\Eventflit($app_key, $app_secret, $app_id, array('notification_host' => 'custom notifications host'))
```
However, note that `notification_host` defaults to `push.eventflit.com` and it is the only supported endpoint.

### Sending native pushes

You can send native notifications by using the `notify` method. The method takes two parameters:

- `interests`: An array of strings which represents the interests your devices are subscribed to. Interests are akin to channels in the DDN. Currently, you can only publish notifications to, at most, _ten_ interests.
- `data`: This represents the payload you'd like to send as part of the notification. You can supply an associative array of keys depending on which platform you'd like to send a notification to. You must include either the `gcm` or `apns` keys. For a detailed list of the acceptable keys, take a look at the docs for [iOS](https://docs.eventflit.com/push_notifications/ios/server) and [Android](https://docs.eventflit.com/push_notifications/android/server).

It also takes a `debug` param like the `trigger` method to allow for debugging.

Example:

```php
$data = array(
  'apns' => array(
    'aps' => array(
      'alert' => array(
        'body' => 'tada'
      ),
    ),
  ),
  'gcm' => array(
    'notification' => array(
      'title' => 'title',
      'icon' => 'icon'
    ),
  ),
);

$eventflit->notify(array("test"), $data);
```

### Errors

Push notification requests, once submitted to the service, are executed asynchronously. To make reporting errors easier, you can supply a `webhook_url` field in the body of the request. The service will call this url with a body that contains the results of the publish request.

Here's an example:

```php
$data = array(
  'apns' => array("..."),
  'gcm' => array("..."),
  'webhook_url' => "http://my.company.com/eventflit/nativepush/results"
);

$eventflit->notify(array("test"), $data);
```

## Debugging & Logging

The best way to debug your applications interaction with server is to set a logger for the library so you can see the internal workings within the library and interactions with the Eventflit service.

### PSR-3 Support

The recommended approach of logging is to use a [PSR-3](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md) compliant logger implementing `Psr\Log\LoggerInterface`. The `Eventflit` object implements `Psr\Log\LoggerAwareInterface`, meaning you call `setLogger(LoggerInterface $logger)` to set the logger instance.

```php
// where $logger implements `LoggerInterface`

$eventflit->setLogger($logger);
```

### Custom Logger (deprecated)

> **Warning**: Using `Eventflit::set_logger()` and a custom object implementing `log()` is now deprecated and will be removed in the future. Please use a PSR-3 compliant logger.

You set up logging by passing an object with a `log` function to the `eventflit->set_logger` function:

```php
class MyLogger {
  public function log( $msg ) {
    print_r( $msg . "\n" );
  }
}

$eventflit->set_logger( new MyLogger() );
```

If you use the above example in code executed from the console/terminal the debug information will be output there. If you use this within a web app then the output will appear within the generated app output e.g. HTML.

## Running the tests

Requires [phpunit](https://github.com/sebastianbergmann/phpunit).

* Run `composer install`
* Go to the `test` directory
* Rename `config.example.php` and replace the values with valid Eventflit credentials **or** create environment variables.
* Some tests require a client to be connected to the app you defined in the config;
  you can do this by opening https://panel.eventflit.com/app#/app/<YOUR_TEST_APP_ID>/#/getting-started in the browser
* From the root directory of the project, execute `composer exec phpunit` to run all the tests.

## Framework Integrations

- **Laravel 5** - https://github.com/eventflit/eventflit-http-laravel/

## License

Copyright 2017, Eventflit. Licensed under the MIT license:
http://www.opensource.org/licenses/mit-license.php

Copyright 2014, Pusher. Licensed under the MIT license:
http://www.opensource.org/licenses/mit-license.php

Copyright 2010, Squeeks. Licensed under the MIT license:
http://www.opensource.org/licenses/mit-license.php
