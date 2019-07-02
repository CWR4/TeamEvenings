<?php

namespace App\Service;

use App\Entity\Movie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class OmdbService
 */
class OmdbService extends AbstractController
{
    // Base url for retrieving all data
    private const BASE_URL_DATA = 'https://www.omdbapi.com/?apikey=';

    /*
     *  Search in open movie database for movie by title
     */
    /**
     * @param array $parameters array with parameters omdb search
     *
     * @return array
     */
    public function searchByTitle(array $parameters) : array
    {
        // Build request url
        $requestUrl = self::BASE_URL_DATA . getenv('OMDB_API_KEY') . '&s=' . $parameters['title'] . '&page=' . $parameters['page'] . '&type=movie';

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

    /*
     *  Retrieve all movie data from omdb
     *  - returns movie entity
     */
    /**
     * @param int $id movie id in online database
     *
     * @return Movie|null
     */
    public function getDataById($id) : ?Movie
    {
        $requestUrl = self::BASE_URL_DATA . getenv('OMDB_API_KEY') . '&i=' . $id;

        $curl = curl_init($requestUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);

        $response = json_decode($response, true);

        if ($response['Response'] === 'True') {
            $m = new Movie();

            $m->setTitle($response['Title']);
            $m->setImdbID($response['imdbID']);
            $m->setYear($response['Year']);
            $m->setPoster($response['Poster']);
            $m->setPlot($response['Plot']);
            $m->setRuntime($response['Runtime']);
        } else {
            $m = null;
        }

        return $m;
    }

    /*
     *  Convert omdb results from json to movie array
     */
    /**
     * @param array $json associative array with movies
     *
     * @return array
     */
    public function getResultsAsEntities(array $json) : array
    {
        $movies = [];
        foreach ($json as $movie) {
            $m = new Movie();
            $m->setTitle($movie['Title']);
            $m->setImdbID($movie['imdbID']);
            $m->setYear($movie['Year']);
            $m->setPoster($movie['Poster']);

            $movies[] = $m;
        }

        return $movies;
    }
}
