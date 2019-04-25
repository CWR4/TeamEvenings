<?php

namespace App\Controller;

use App\Form\MovieFormType;
use App\Service\OmdbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Movie;

class OmdbController extends AbstractController
{
    /**
     * @Route("/omdb", name="omdb")
     */
    public function searchInOmdb(OmdbService $omdbService, Request $request)
    {
        $form = $this->createForm(MovieFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted())
        {
            $title = $form->get('Title')->getData();
            dump($title);
            $omdbService->searchByTitle($title);
            $omdbService->getDataById('tt0081505');
        }

        return $this->render('omdb/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
