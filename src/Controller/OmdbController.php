<?php

namespace App\Controller;

use App\Entity\MovieNight;
use App\Form\AddMovieType;
use App\Form\MovieFormType;
use App\Service\OmdbService;
use App\Service\PaginationService;
use App\Service\VotingService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

/**
 * Class OmdbController
 * @IsGranted("ROLE_USER")
 */

class OmdbController extends AbstractController
{
    /**
     * @var OmdbService
     */
    private $omdbService;

    /**
     * @var VotingService
     */
    private $votingService;

    /**
     * @var PaginationService
     */
    private $paginationService;

    /**
     * OmdbController constructor.
     * @param OmdbService       $omdbService       dependency injection
     * @param VotingService     $votingService     dependency injection
     * @param PaginationService $paginationService dependency injection
     */
    public function __construct(OmdbService $omdbService, VotingService $votingService, PaginationService $paginationService)
    {
        $this->omdbService = $omdbService;
        $this->votingService = $votingService;
        $this->paginationService = $paginationService;
    }

    /**
     *  - search movie in open movie database
     *  - relate movie to movienight
     *  - store movie in database
     *
     * @param Request    $request    http request
     * @param MovieNight $movieNight movienight
     * @param int        $movie      movie id
     * @param int        $page       current page
     * @param string     $title      title of movie as string
     *
     * @throws Exception
     *
     * @return Response
     *
     * @Route("/omdb/{movieNight}/{movie<\d+>?0}/{title<.*?>?}/{page<\d+>?1}", name="omdb")
     */
    public function addOmdbMovie(Request $request, MovieNight $movieNight, $movie, $page, $title) : Response
    {
        // Create form for movie search
        $form = $this->createForm(MovieFormType::class);
        $form->handleRequest($request);

        // Set parameters for pagination and api call
        $parameters = ['movieNight' => $movieNight->getId(), 'title' => $title, 'page' => $page, 'movie' => $movie];

        // Set variables to null, so they won't show if not needed
        $movies = [];
        $pagination = [];

        // Process form, get pagination and movies
        $this->omdbService->processAndUpdateOmdbRequest($this->paginationService, $form, $parameters, $pagination, $movies);

        // Create form to add movie to event
        $addForm = $this->createForm(AddMovieType::class);
        $addForm->add('mid', HiddenType::class, ['data' => $movie]);
        $addForm->handleRequest($request);

        // Check if form was send and process it
        if ($this->omdbService->processAddForm($this->votingService, $addForm, $movieNight)) {
            return $this->redirectToRoute('movie_night_add_movie', ['movieNight' => $movieNight->getId()]);
        }

        return $this->render('omdb/index.html.twig', [
            'form' => $form->createView(),
            'movies' => $movies,
            'pagination' => $pagination,
            'title' => urldecode($parameters['title']),
            'addform' => $addForm->createView(),
            'date' => $movieNight,
        ]);
    }
}
