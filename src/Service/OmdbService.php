<?php

namespace App\Service;

use phpDocumentor\Reflection\Types\Mixed_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class OmdbService extends AbstractController
{
    // Base url for retrieving all data
    private const BASE_URL_DATA = 'https://www.omdbapi.com/?apikey=';
    // Base url for retrieving poster to movie
    private const BASE_URL_POSTER = 'https://img.omdbapi.com/?apikey=';


    public function searchByTitle($title, $page = null)
    {
        $title = urlencode($title);
        $requestUrl = self::BASE_URL_DATA . getenv('OMDB_API_KEY') . '&s=' . $title;
        if($page !== null)
        {
            $requestUrl = $requestUrl . '&page=' . $page;
        }
        dump($requestUrl);

        $curl = curl_init($requestUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $json = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($json, true);

        if($result['Response'] === 'False')
        {
            return $result['Error'];
        }

        $movies = [];

        return $movies;
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