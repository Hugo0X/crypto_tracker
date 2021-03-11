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

    public function isCryptoExist($crypto)
    {
        function get_http_response_code($url) {
            $headers = get_headers($url);
            return substr($headers[0], 9, 3);
        }

        $url = $this->apiLink . 'coins/'. str_replace(' ', '-', strtolower($crypto));

        if(get_http_response_code($url) != "200"){
            return false;
        }
        else{
            return true;
        }
    }
            
}