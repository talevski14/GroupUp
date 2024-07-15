<?php

namespace Services;

interface CommentService
{
    public function addComment(object|array|null $data, int $userId): int;
}