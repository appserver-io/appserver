<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;

require_once __DIR__ . '/vendor/autoload.php';

$exchange = 'router';
$queue = 'msgs';
$consumerTag = 'consumer';

// $connection = new AMQPStreamConnection('127.0.0.1', 8587, 'guest', 'guest');
$connection = new AMQPStreamConnection('127.0.0.1', 5672, 'guest', 'guest');
$channel = $connection->channel();

/*
$channel->queue_declare($queue, false, true, false, false);
$channel->exchange_declare($exchange, 'direct', false, true, false);
$channel->queue_bind($queue, $exchange);
*/
