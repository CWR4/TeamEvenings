<?php

namespace App\Controller;

use App\Entity\MovieNight;
use App\Form\MovieFormType;
use App\Service\OmdbService;
use App\Service\PaginationService;

use Exception;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class OmdbController
 * @IsGranted("ROLE_ADMIN")
 */
class OmdbController extends AbstractController
{
    /**
     * @var OmdbService
     */
    private $omdbService;

    /**
     * @var PaginationService
     */
    private $paginationService;

    /**
     * OmdbController constructor.
     * @param OmdbService       $omdbService       dependency injection
     * @param PaginationService $paginationService dependency injection
     */
    public function __construct(OmdbService $omdbService, PaginationService $paginationService)
    {
        $this->omdbService = $omdbService;
        $this->paginationService = $paginationService;
    }

    /**
     *  - search movie in open movie database
     *  - relate movie to movienight
     *  - store movie in database
     *
     * @Route("/omdb/{movieNight}/{title<.*?>?}/{page<\d+>?1}", name="omdb")
     *
     * @param Request    $request    http request
     * @param MovieNight $movieNight movienight
     * @param int        $page       current page
     * @param string     $title      title of movie as string
     *
     * @throws Exception
     *
     * @return Response
     */
    public function addOmdbMovie(Request $request, MovieNight $movieNight, $page, $title) : Response
    {
        // Create form for movie search
        $form = $this->createForm(MovieFormType::class);
        $form->handleRequest($request);

        // Set parameters for pagination and api call
        $parameters = ['movieNight' => $movieNight->getId(), 'title' => $title, 'page' => $page];

        // Set variables to null, so they won't show if not needed
        $movies = [];
        $pagination = [];

        // Process form, get pagination and movies
        $this->omdbService->processAndUpdateOmdbRequest($this->paginationService, $form, $parameters, $pagination, $movies);

        return $this->render('omdb/index.html.twig', [
            'form' => $form->createView(),
            'movies' => $movies,
            'pagination' => $pagination,
            'title' => urldecode($parameters['title']),
            'movieNight' => $movieNight,
        ]);
    }
}
