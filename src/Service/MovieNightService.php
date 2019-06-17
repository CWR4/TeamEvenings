<?php

namespace App\Service;

use App\Entity\MovieNight;
use App\Entity\Voting;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;

class MovieNightService extends AbstractController
{
    /**
     * @param FormInterface $form
     * @param MovieNight $movieNight
     * @return bool
     */
    private function validateMovieNightForm(FormInterface $form, MovieNight $movieNight): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            if ($movieNight->getDate() !== null && $movieNight->getDate()->format('Y.m.d') < date('Y.m.d')) {
                $this->addFlash('warning', 'Datum ist vergangen!');
            } elseif ($movieNight->getDate()->format('d.m.Y') === date('d.m.Y')
                && $movieNight->getTime()->format('H:i') < date('H:i', time() - 900)) {
                $this->addFlash('warning', 'Zeitpunkt ist vergangen!');
            } else {
                return true;
            }
        }
        return false;
    }

    /**
     * @param FormInterface $form
     * @param MovieNight $movieNight
     * @return bool
     */
    public function createMovieNight(FormInterface $form, MovieNight $movieNight): bool
    {
        if ($form->isSubmitted() && $form->isValid() && $this->validateMovieNightForm($form, $movieNight)) {
            $manager = $this->getDoctrine()->getManager();

            $voting = new Voting();
            $voting->setOpen(true);

            $movieNight->setVoting($voting);

            $manager->persist($voting);
            $manager->persist($movieNight);

            $manager->flush();
            $this->addFlash('success', 'Termin erstellt!');

            return true;
        }
        return false;
    }

    /**
     * @param FormInterface $form
     * @param MovieNight $movieNight
     * @return bool
     */
    public function editMovieNight(FormInterface $form, MovieNight $movieNight): bool
    {
        if ($form->isSubmitted() && $form->isValid() && $this->validateMovieNightForm($form, $movieNight)) {
            $manager = $this->getDoctrine()->getManager();

            $manager->persist($movieNight);
            $manager->flush();

            $this->addFlash('success', 'Termin erfolgreich geändert');

            return true;
        }
        return false;
    }

}