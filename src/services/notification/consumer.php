<?php

require 'vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Services\implementation\NotificationServiceImpl;

require_once 'src/services/implementation/NotificationServiceImpl.php';
$entityManager = require __DIR__ . "/../../../config/doctrine.php";

try {
    $connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
} catch (Exception $e) {
    echo "Not connected";
    return 0;
}
$channel = $connection->channel();

$channel->queue_declare('event_notifications', false, false, false, false);

while (true) {
    $message = $channel->basic_get('event_notifications');
    if ($message) {
        $data = json_decode($message->getBody(), true);
        $userId = $data['user_id'];
        $eventId = $data['event_id'];

//        echo " [x] Sent notification to user $userId for event $eventId\n";
        $notificationService = new NotificationServiceImpl($entityManager);
        $notificationService->sendMailToUserAboutEvent($userId, $eventId);

        $channel->basic_ack($message->getDeliveryTag());
    } else {
        break;
    }
}
$channel->close();
$connection->close();