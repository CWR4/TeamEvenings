<?php

namespace App\Controller;

use App\Entity\MovieNight;

use Exception;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BaseController
 */
class BaseController extends AbstractController
{
    /**
     *  - landing page
     *  - displays next event
     *
     * @Route("/", name="base")
     *
     * @throws Exception
     *
     * @return Response
     */
    public function landingPage() : Response
    {
        $movieNight = $this->getDoctrine()->getRepository(MovieNight::class)->getNextMovienight();
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
