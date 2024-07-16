<?php

namespace Services\caching;

interface DataServiceInterface
{
    public function getCommentsForEvent($id): ?array;
}