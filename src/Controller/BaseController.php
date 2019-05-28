<?php

namespace App\Controller;

use App\Entity\MovieNight;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    /*
     *  - landing page
     *  - displays next event
     */
    /**
     * @Route("/", name="base")
     */
    public function index()
    {
        $movienight = $this->getDoctrine()->getRepository(MovieNight::class)->getNextMovienight();

        return $this->render('base/index.html.twig', [
            'movienight' => $movienight,
        ]);
    }
}
