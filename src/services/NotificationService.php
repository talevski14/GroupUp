<?php

namespace Services;

use Models\Event;

interface NotificationService
{
    public function queueNotification(Event $event);

    public function sendMailToUserAboutEvent(int $userId, int $eventId): void;
}