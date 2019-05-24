<?php

namespace App\Controller;

use App\Entity\MovieNight;
use App\Entity\Movie;
use App\Form\MovieNightType;
use App\Form\EditMovieNightType;
use App\Service\OmdbService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MovieNightController
 * @package App\Controller
 * @IsGranted("ROLE_USER")
 */

class MovieNightController extends AbstractController
{
    /**
     * @Route("/movienight/create", name="movie_night")
     */
    public function createMovieNight(Request $request, OmdbService $omdbService) : Response
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
            elseif($movienight->getDate()->format('d.m.Y') ===  date('d.m.Y') && $movienight->getTime()->format('H:i') < date('H:i', time() - 900))
            {
                $this->addFlash('warning', 'Zeitpunkt ist vergangen!');
            }
            else
            {
//                $movie = $omdbService->getDataById('tt0081505');
//
//                if($this->getDoctrine()->getRepository(Movie::class)->findByImdbId($movie->getImdbID()))
//                {
//                    $movie = $this->getDoctrine()->getRepository(Movie::class)->findByImdbId($movie->getImdbID());
//                    $movie->addMovieNight($movienight);
//                }
//                else
//                {
//                    $movienight->setMovie($movie);
//                }
//
//                $manager->persist($movie);
                $manager->persist($movienight);
                $manager->flush();
                $this->addFlash('success', 'Termin erstellt!');

                return $this->redirectToRoute('list_movienight');
            }
        }

        return $this->render('movie_night/index.html.twig', [
            'form' => $dateform->createView()
        ]);
    }

    /**
     * @Route("/movienight/all", name="list_movienight")
     */
    public function listAll() : Response
    {
        $manager = $this->getDoctrine()->getRepository(MovieNight::class);

        $dates = $manager->findAllByDateAsc();

        dump($dates);

        return $this->render('movie_night/list.html.twig', [
            'dates' => $dates
        ]);
    }

    /**
     * @Route("/movienight/edit/{id<\d+>}", name="edit_movienight")
     */
    public function editMovieNight(Request $request, $id) : Response
    {
        $manager = $this->getDoctrine()->getManager();
        $date = $manager->getRepository(MovieNight::class)->find($id);

        if($date === null)
        {
            $this->addFlash('warning', 'Termin wurde nicht gefunden');
            return $this->redirectToRoute('list_movienight');
        }

        $editForm = $this->createForm(EditMovieNightType::class, $date);
        $editForm->handleRequest($request);

        if($editForm->isSubmitted() && $editForm->isValid())
        {
            if($date->getDate()->format('Y.m.d') < date('Y.m.d'))
            {
                $this->addFlash('warning', 'Datum ist vergangen!');
            }
            elseif($date->getDate()->format('d.m.Y') ===  date('d.m.Y') && $date->getTime()->format('H:i') < date('H:i', time() - 900))
            {
                $this->addFlash('warning', 'Zeitpunkt ist vergangen!');
            }
            else
            {
                $manager->persist($date);
                $manager->flush();

                $this->addFlash('success', 'Termin erfolgreich geändert');

                return $this->redirectToRoute('list_movienight');
            }
        }

        return $this->render('movie_night/edit.html.twig', [
            'form' => $editForm->createView()
        ]);
    }

}
