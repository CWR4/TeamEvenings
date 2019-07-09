<?php

namespace App\Service;

use App\Entity\Movie;
use App\Entity\MovieNight;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;

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
     * @param PaginationService $paginationService pagination service
     * @param FormInterface     $form              search form
     * @param array             $parameters        reference for pagination links
     * @param array             $pagination        reference for pagination links
     * @param array             $movies            reference for movies
     */
    public function processAndUpdateOmdbRequest(PaginationService $paginationService, FormInterface $form, array &$parameters, array &$pagination, array &$movies): void
    {
        if (isset($parameters['title']) || ($form->isSubmitted() && $form->isValid())) {
            // If form was submitted get new movie title from form and set current page to 1 (for API call)
            if ($form->isSubmitted()) {
                $parameters['title'] = urlencode($form->get('Title')->getData());
                $parameters['page'] = 1;
            }

            // API call
            $result = $this->searchByTitle($parameters);

            // Check if movies found and if pagination is needed
            if ($result['Response'] === 'True' && $result['totalResults'] > 10) {
                $paginationService->createPagination('omdb', $parameters, $result['totalResults']);
                $pagination = $paginationService->getPaginationLinks();
            }

            // Check for errors and create flash message
            // else: Not enough movies found for pagination
            if ($result['Response'] === 'False') {
                if ($result['Error'] === 'Too many results.') {
                    $this->addFlash('warning', 'Zu viele Ergebnisse. Bitte spezifizieren.');
                } elseif ($result['Error'] === 'Movie not found!') {
                    $this->addFlash('warning', 'Kein Film gefunden.');
                } else {
                    $this->addFlash('warning', $result['Error']);
                }
            } else {
                $movies = $this->getResultsAsEntities($result['Search']);
            }
        }
    }

    /**
     * @param MovieNight $movieNight movienight
     * @param string     $imdbId     omdb movie id
     *
     * @return void
     */
    public function addMovie(MovieNight $movieNight, string $imdbId): void
    {
        // Get movie information from omdb
        $manager = $this->getDoctrine()->getManager();

        $movie = $manager->getRepository(Movie::class)->findByImdbId($imdbId);

        // Check if movie already exist in db
        if (null !== $movie) {
            if (in_array($movie, $movieNight->getMovies()->toArray(), true)) {
                $this->addFlash('warning', 'Film bereits zugeordnet!');
            }
            $movie->addMovieNight($movieNight);
        } else {
            $movie = $this->getDataById($imdbId);
            $movieNight->addMovie($movie);
            $manager->persist($movie);
            $this->addFlash('success', 'Film erfolgreich hinzugefügt');
        }
        $manager->flush();
    }
}
