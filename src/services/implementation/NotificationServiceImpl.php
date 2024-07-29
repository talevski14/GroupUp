<?php

namespace Services\implementation;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use Exception;
use Models\Event;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Services\NotificationService;

class NotificationServiceImpl implements NotificationService
{
    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @throws Exception
     */
    public function queueNotification(Event $event): void
    {
        $society = $event->getSociety();
        $members = $society->getMembers();

        foreach ($members as $member) {
            $this->sendNotificationToQueue($member->getUserId(), $event->getId());
        }
    }

    /**
     * @throws Exception
     */
    private function sendNotificationToQueue($userId, $eventId): void
    {
        $connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->queue_declare('event_notifications', false, false, false, false);

        $data = json_encode(['user_id' => $userId, 'event_id' => $eventId]);
        $msg = new AMQPMessage($data);

        $channel->basic_publish($msg, '', 'event_notifications');

        $channel->close();
        $connection->close();
    }
}