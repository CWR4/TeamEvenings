<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Movie;


class OmdbService extends AbstractController
{
    // Base url for retrieving all data
    private const BASE_URL_DATA = 'https://www.omdbapi.com/?apikey=';
    // Base url for retrieving poster to movie
    private const BASE_URL_POSTER = 'https://img.omdbapi.com/?apikey=';

    public function searchByTitle($title, $page) : array
    {
        // Encode input for url (replace space with +)
        $title = urlencode($title);

        // Build request url
        $requestUrl = self::BASE_URL_DATA . getenv('OMDB_API_KEY') . '&s=' . $title . '&page=' . $page;

        // Initialize curl session(http request)
        $curl = curl_init($requestUrl);

        // Set options, return string instead of render it directly
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // Execute http request
        $json = curl_exec($curl);

        // Close curl session
        curl_close($curl);

        // Return associative array
        return  json_decode($json, true);
    }

    public function getDataById($id) : void
    {
        $requestUrl = self::BASE_URL_DATA . getenv('OMDB_API_KEY') . '&i=' . $id;
        dump($requestUrl);

        $curl = curl_init($requestUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        dump($response);
    }

    public function getPosterById($imdbID)
    {
        // Build request url
        $requestUrl = self::BASE_URL_POSTER . getenv('OMDB_API_KEY') . '&i=' . $imdbID;

        // Initialize curl session(http request)
        $curl = curl_init($requestUrl);

        // Set options, return string instead of render it directly
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // Execute http request
        $poster = curl_exec($curl);

        // Close curl session
        curl_close($curl);

        // Return poster
        return $poster;
    }

    public function getResultsAsEntities($json) : array
    {
        $movies = [];

        foreach ($json as $movie)
        {
            $m = new Movie();

            $m->setTitle($movie['Title']);
            $m->setImdbID($movie['imdbID']);
            $m->setYear($movie['Year']);
            //$m->setPoster($this->getPosterById($movie['imdbID']));

            $movies[] = $m;
        }

        return $movies;
    }

}