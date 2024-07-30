<?php

namespace Services\implementation;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use Exception;
use Models\Event;
use Models\User;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PHPMailer\PHPMailer\PHPMailer;
use Services\NotificationService;

class NotificationServiceImpl implements NotificationService
{
    private EntityManagerInterface $entityManager;
    protected AMQPStreamConnection $connection;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');;
    }

    /**
     * @throws Exception
     */
    public function queueNotification(Event $event): void
    {
        $society = $event->getSociety();
        $members = $society->getMembers();

        $connection = $this->connection;
        $channel = $connection->channel();
        $channel->queue_declare('event_notifications', false, false, false, false);

        foreach ($members as $member) {
            $this->sendNotificationToQueue($member->getUserId(), $event->getId(), $channel);
        }

        $channel->close();
        $connection->close();
    }

    /**
     * @throws Exception
     */
    private function sendNotificationToQueue($userId, $eventId, $channel): void
    {
        $data = json_encode(['user_id' => $userId, 'event_id' => $eventId]);
        $msg = new AMQPMessage($data);

        $channel->basic_publish($msg, '', 'event_notifications');
    }

    public function sendMailToUserAboutEvent($userId, $eventId): void
    {
        $user = $this->entityManager->getRepository(User::class)->find($userId);
        $event = $this->entityManager->getRepository(Event::class)->find($eventId);

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'mailhog';  // MailHog container name
            $mail->SMTPAuth = false;  // MailHog does not require authentication
            $mail->Port = 1025;       // MailHog SMTP port

            $mail->setFrom('groupup@groupup.com', 'GroupUp');
            $mail->addAddress($user->getUsername() . '@groupup.com');

            $mail->isHTML(true);

            $userName = $user->getName();
            $eventName = $event->getName();
            $eventTime = $event->getDateAndTime()->format("H:i");
            $eventDate = $event->getDateAndTime()->format("Y-m-d");
            $eventLocation = $event->getLocation();
            $eventDescription = $event->getDescription();
            $invitorName = $event->getCreator()->getName();
            $societyName = "ime";

            $mail->Subject = "Hey {$userName}, you've been invited to {$eventName}!";
            $mail->Body = " <h2>Hello {$userName},</h2>
        <p>You have been invited to the following event by {$invitorName}:</p>
        <p><strong>Event Name:</strong> {$eventName}</p>
        <p><strong>Date:</strong> {$eventDate}</p>
        <p><strong>Time:</strong> {$eventTime}</p>
        <p><strong>Location:</strong> {$eventLocation}</p>
        <p><strong>Description:</strong> {$eventDescription}</p>
        <p>We hope to see you there!</p>";

            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

    }
}