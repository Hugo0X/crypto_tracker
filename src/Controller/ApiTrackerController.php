<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Codenixsv\CoinGeckoApi\CoinGeckoClient;

class ApiTrackerController extends AbstractController
{
    private $apiLink = 'https://api.coingecko.com/api/v3/';

    public function getCurrency($crypto)
    {
        $response = file_get_contents( $this->apiLink . 'simple/price?ids='. $crypto .'&vs_currencies=eur');
        $response = json_decode($response);
        $r = $response->$crypto->eur;
        return $r;
    }

    public function getAcronym($crypto)
    {
        $response = file_get_contents( $this->apiLink . 'coins/'. $crypto);
        $response = json_decode($response);
        $r = strtoupper($response->symbol);
        return $r;
    }

    public function getImageUrl($crypto)
    {
        $response = file_get_contents( $this->apiLink . 'coins/'. $crypto);
        $response = json_decode($response);
        $r = $response->image->small;
        return $r;
    }

    public function getAllCrypto()
    {
        $response = file_get_contents( $this->apiLink . 'coins/list?include_platform=false');
        $response = json_decode($response);
        $r = array_column($response, 'id');
        return $r;
    }

    public function isCryptoExist($crypto)
    {
        $dashUrl = $this->apiLink . 'coins/'. str_replace(' ', '-', strtolower($crypto));
        $spaceUrl = $this->apiLink . 'coins/'. str_replace(' ', '', strtolower($crypto));


        if($this->get_http_response_code($dashUrl) == "200")
            return 'dash';
        elseif($this->get_http_response_code($spaceUrl) == "200")
            return 'space';
        else
            return false;    
    }

    private function get_http_response_code($url) {
        $headers = get_headers($url);
        return substr($headers[0], 9, 3);
    }
}