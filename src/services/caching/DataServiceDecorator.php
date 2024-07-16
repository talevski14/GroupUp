<?php

namespace Services\caching;

class DataServiceDecorator implements DataServiceInterface
{
    protected $dataService;

    /**
     * @param DataServiceInterface $dataService
     */
    public function __construct(DataServiceInterface $dataService)
    {
        $this->dataService = $dataService;
    }

    public function getCommentsForEvent($id): ?array
    {
        return $this->dataService->getCommentsForEvent($id);
    }
}