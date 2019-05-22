<?php

namespace App\Controller;

use App\Entity\MovieNight;
use App\Form\MovieNightType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieNightController extends AbstractController
{
    /**
     * @Route("/movienight/create", name="movie_night")
     */
    public function createMovieNight(Request $request) : Response
    {
        $manager = $this->getDoctrine()->getManager();

        $movienight = new MovieNight();
        $dateform = $this->createForm(MovieNightType::class, $movienight);
        $dateform->handleRequest($request);

        if($dateform->isSubmitted() && $dateform->isValid())
        {
            if($movienight->getDate()->format('Y.m.d') < date('Y.m.d'))
            {
                $this->addFlash('warning', 'Datum ist vergangen!');
            }
            elseif($movienight->getDate()->format('d.m.Y') ===  date('d.m.Y') && $movienight->getTime()->format('H:i') < date('H:i', time() - 3600))
            {
                $this->addFlash('warning', 'Zeitpunkt ist vergangen!');
            }
            else
            {
                $manager->persist($movienight);
                $manager->flush();
                $this->addFlash('success', 'Termin erstellt!');
            }
        }

        return $this->render('movie_night/index.html.twig', [
            'form' => $dateform->createView()
        ]);
    }
}
