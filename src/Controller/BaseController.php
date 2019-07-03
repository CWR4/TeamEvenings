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
     * @param VotingService     $votingService     dependency injection
     * @param MovieNightService $movieNightService dependency injection
     *
     * @throws Exception
     *
     * @return Response
     *
     * @Route("/", name="base")
     */
    public function index(VotingService $votingService, MovieNightService $movieNightService) : Response
    {
        $movieNight = $movieNightService->getNextMovieNight();

        $votingService->updateMovieNightMovie($movieNight);

        return $this->render('base/index.html.twig', [
            'movienight' => $movieNight,
        ]);
    }
}
