<?php

namespace Services\caching;

use Predis\Client;

class CachingDataService extends DataServiceDecorator
{
    private $redis;

    public function __construct(DataServiceInterface $dataService, Client $client)
    {
        parent::__construct($dataService);
        $this->redis = $client;
    }

    public function getCommentsForEvent($id): ?array
    {
        $key = "event_{$id}_comments";
        if($this->redis->exists($key)) {
            $cachedData = $this->redis->get($key);
//            echo "cache ";
            return json_decode($cachedData);
        } else {
            $data = parent::getCommentsForEvent($id);
//            echo "db";
            $this->redis->set($key, json_encode($data));
            $this->redis->expire($key, 600);
            return $data;
        }
    }
}