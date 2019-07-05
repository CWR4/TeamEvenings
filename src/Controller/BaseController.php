<?php

namespace App\Controller;

use App\Service\MovieNightService;
use App\Service\VotingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

/**
 * Class BaseController
 */
class BaseController extends AbstractController
{
    /*
     *  - landing page
     *  - displays next event
     */
    /**
     * @param MovieNightService $movieNightService dependency injection
     *
     * @throws Exception
     *
     * @return Response
     *
     * @Route("/", name="base")
     */
    public function index(MovieNightService $movieNightService) : Response
    {
        $movieNight = $movieNightService->getNextMovieNight();
        if ($movieNight) {
            $movie = $movieNight->getVotedMovie();
        } else {
            $movie = null;
        }

        return $this->render('base/index.html.twig', [
            'movienight' => $movieNight,
            'movie' => $movie,
        ]);
    }
}
