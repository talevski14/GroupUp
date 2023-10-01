<?php

namespace Core;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Weather
{
    private Client $client;
    private array $config;
    public function __construct($client, $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * @throws GuzzleException
     */
    public function getWeather($date, $lat, $lon): array
    {
        $client = $this->client;

        $weatherConf = $this->config['weather'];
        $query = http_build_query($weatherConf['query']);
        $url = $weatherConf['url'];

        $temperature = null;
        $rain = null;
        $snow = null;

        $responseAPI = $client->get("$url" . "?latitude=" . $lat . "&longitude=" . $lon . "&" . "$query");
        $weather = get_object_vars(json_decode($responseAPI->getBody()->getContents()));

        $timeAPI = get_object_vars($weather["hourly"])["time"];
        $timeAPI = array_search("$date", $timeAPI);

        if($timeAPI != ""){
            $temperature = get_object_vars($weather["hourly"])["temperature_2m"]["$timeAPI"];
            $rain = get_object_vars($weather["hourly"])["rain"]["$timeAPI"] != "0.00" ? get_object_vars($weather["hourly"])["rain"]["$timeAPI"] : null;
            $snow = get_object_vars($weather["hourly"])["snowfall"]["$timeAPI"] != "0.00" ? get_object_vars($weather["hourly"])["snowfall"]["$timeAPI"] : null;
        }

        return [
            "temperature" => $temperature,
            "rain" => $rain,
            "snow" => $snow
        ];
    }



}