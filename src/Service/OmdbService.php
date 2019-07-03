<?php

namespace App\Service;

use App\Entity\Movie;
use App\Entity\MovieNight;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Exception;

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
     * @return Movie
     */
    public function getDataById($id) : Movie
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

    /**
     * @param VotingService $votingService dependency injection
     * @param FormInterface $addForm       to add movie to movienight and database
     * @param MovieNight    $movienight    to edit
     *
     * @throws Exception
     *
     * @return bool
     */
    public function processAddForm(VotingService $votingService, FormInterface $addForm, MovieNight $movienight): bool
    {
        if ($addForm->isSubmitted()) {
            // Get movie information from omdb
            $movieid = $addForm->getData()['movieid'];
            $movie = $this->getDataById($movieid);

            $manager = $this->getDoctrine()->getManager();

            // Check if movie already exist in db
            if ($this->getDoctrine()->getRepository(Movie::class)->findByImdbId($movie->getImdbID())) {
                $movie = $manager->getRepository(Movie::class)->findByImdbId($movie->getImdbID());
                $movie->addVoting($movienight->getVoting());
            } else {
                $movienight->getVoting()->addMovie($movie);
            }

            if (!($addForm->getData()['mid'] === '0' || $addForm->getData()['mid'] === null)) {
                $oldmovie = $this->getDoctrine()->getRepository(Movie::class)->find($addForm->getData()['mid']);
                $movienight->getVoting()->removeMovie($oldmovie);
                $votingService->deleteVotes($movienight->getVoting(), $oldmovie);
            }

            $manager->persist($movie);
            $manager->persist($movienight->getVoting());
            $manager->flush();

            $this->addFlash('success', 'Film erfolgreich hinzugefügt');

            return true;
        }

        return false;
    }
}
