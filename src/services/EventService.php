<?php

namespace Services;

use Doctrine\Common\Collections\Collection;
use Models\Event;
use Models\Society;
use Models\User;
use Rakit\Validation\Validation;

interface EventService
{
    public function checkIfPassed(Event $event) : Event;

    public function getEventsForSociety(int $societyId): Collection;

    public function getOnGoingEventsForSocietyDisplay(int $societyId): ?array;

    public function getPassedEventsForSocietyDisplay(int $societyId): ?array;

    public function getAttendeesForEventDisplay(Event $event): array;

    public function getUserAttendingEvent(String $username, Event $event): bool;

    public function getCommentsForEventDisplay(Event $event): array;

    public function getCreatorForEventDisplay(Event $event): array;

    public function getUserCanEditEvent(string $username, Event $event): bool;

    public function getEventCreatedOn(Event $event): string;

    public function getEventDate(Event $event): string;

    public function getEventTime(Event $event): string;

    public function getWeatherEvent(Event $event): string;

    public function validateEvent(object|array|null $data): Validation;

    public function addEvent(object|array|null $data, int $creatorId, int $societyId): Event;

    public function attendEvent(int $userId, int $eventId, string $response): int;

    public function deleteEvent(int $eventID): int;

    public function updateEvent(object|array|null $data, int $idEvent): int;

    public function getEventById(int $id): Event;

    public function getEventByIdForDisplay(int $id): array;
}