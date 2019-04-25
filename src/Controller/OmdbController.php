<?php

namespace App\Controller;

use App\Form\MovieFormType;
use App\Service\OmdbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Movie;

class OmdbController extends AbstractController
{
    /**
     * @Route("/omdb", name="omdb")
     */
    public function searchInOmdb(OmdbService $omdbService, Request $request) : Response
    {
        $form = $this->createForm(MovieFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted())
        {
            $title = $form->get('Title')->getData();

            $result = $omdbService->searchByTitle($title);

            if($result['Response'] === 'False')
            {
                if($result['Error'] === 'Too many results.')
                {
                    $this->addFlash('warning', 'Zu viele Ergebnisse. Bitte spezifizieren.');
                }
                elseif($result['Error'] === 'Movie not found.')
                {
                    $this->addFlash('warning', 'Kein Film gefunden');
                }
                else
                {
                    $this->addFlash('warning', $result['Error']);
                }
            }
        }

        return $this->render('omdb/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
