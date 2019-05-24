<?php

namespace App\Controller;

use App\Entity\MovieNight;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    /**
     * @Route("/", name="base")
     */
    public function index()
    {
        $movienight = $this->getDoctrine()->getRepository(MovieNight::class)->getNextMovienight();

        dump($movienight);

        return $this->render('base/index.html.twig', [
            'movienight' => $movienight,
        ]);
    }

    /**
     * @Route("/hallo", name="hallo")
     */
    public function hallo()
    {
        return $this->render('base/hallo.html.twig', [
        ]);
    }
}
