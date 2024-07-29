<?php

namespace Services;

use Models\Event;

interface NotificationService
{
    public function queueNotification(Event $event);
}