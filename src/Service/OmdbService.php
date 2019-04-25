<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class OmdbService extends AbstractController
{
    // Base url for retrieving all data
    private const BASE_URL_DATA = 'http://www.omdbapi.com/?apikey=';
    // Base url for retrieving poster to movie
    private const BASE_URL_POSTER = 'http://img.omdbapi.com/?apikey=';


    public function searchByTitle($title)
    {
        dump($title);
        $requestUrl = self::BASE_URL_DATA . getenv('OMDB_API_KEY') . '&s=' . $title;
        dump($requestUrl);

        $curl = curl_init($requestUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        dump($response);
    }

    public function getDataById($id)
    {
        $requestUrl = self::BASE_URL_DATA . getenv('OMDB_API_KEY') . '&i=' . $id;
        dump($requestUrl);

        $curl = curl_init($requestUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        dump($response);
    }

}