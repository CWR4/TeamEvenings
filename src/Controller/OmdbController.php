<?php

namespace App\Controller;

use App\Form\MovieFormType;
use App\Service\OmdbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OmdbController extends AbstractController
{
    /**
     * @Route("/omdb/{page<\d+>?1}", name="omdb")
     */
    public function searchInOmdb(OmdbService $omdbService, Request $request, $page) : Response
    {
        $form = $this->createForm(MovieFormType::class);
        $form->handleRequest($request);

        $movies = null;

        if($form->isSubmitted())
        {
            $title = $form->get('Title')->getData();

            $result = $omdbService->searchByTitle($title, $page);
            dump($result);

            if($result['Response'] === 'False')
            {
                if($result['Error'] === 'Too many results.')
                {
                    $this->addFlash('warning', 'Zu viele Ergebnisse. Bitte spezifizieren.');
                }
                elseif($result['Error'] === 'Movie not found!')
                {
                    $this->addFlash('warning', 'Kein Film gefunden.');
                }
                else
                {
                    $this->addFlash('warning', $result['Error']);
                }
            }
            else
            {
                $movies = $omdbService->getResultsAsEntities($result['Search']);
            }
        }

        return $this->render('omdb/index.html.twig', [
            'form' => $form->createView(),
            'movies' => $movies
        ]);
    }
}
