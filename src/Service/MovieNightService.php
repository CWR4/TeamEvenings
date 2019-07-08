<?php

namespace App\Service;

use App\Entity\MovieNight;
use App\Entity\Voting;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Exception;

/**
 * Class MovieNightService
 */
class MovieNightService extends AbstractController
{
    /**
     * @param FormInterface $form       dependency injection for form interface -> validation
     * @param MovieNight    $movieNight movienight as parameter
     *
     * @return bool
     */
    public function createMovieNight(FormInterface $form, MovieNight $movieNight): bool
    {
        if ($form->isSubmitted() && $form->isValid() && $this->validateMovieNightForm($form, $movieNight)) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($movieNight);
            $manager->flush();
            $this->addFlash('success', 'Termin erstellt!');

            return true;
        }

        return false;
    }

    /**
     * @param FormInterface $form       dependency injection for form interface -> validation
     * @param MovieNight    $movieNight movienight as parameter
     *
     * @return bool
     */
    public function editMovieNight(FormInterface $form, MovieNight $movieNight): bool
    {
        if ($form->isSubmitted() && $form->isValid() && $this->validateMovieNightForm($form, $movieNight)) {
            $manager = $this->getDoctrine()->getManager();
            $manager->flush();

            $this->addFlash('success', 'Termin erfolgreich geändert');

            return true;
        }

        return false;
    }

    /**
     * @param FormInterface $form       dependency injection for form interface -> validation
     * @param MovieNight    $movieNight movienight as parameter
     *
     * @return bool
     */
    public function deleteMovieNight(FormInterface $form, MovieNight $movieNight): bool
    {
        if ($form->isSubmitted()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->remove($movieNight);
            $manager->flush();


            return true;
        }

        return false;
    }

    /**
     * @throws Exception
     *
     * @return MovieNight|null
     */
    public function getNextMovieNight(): ?MovieNight
    {
        $i = 0;
        do {
            $movieNight = $this->getDoctrine()->getRepository(MovieNight::class)->getNextMovienight($i);
            $i++;
        } while (isset($movieNight) && $movieNight->getMovies()->isEmpty());

        return $movieNight;
    }

    /**
     * @param FormInterface $form       dependency injection for form interface -> validation
     * @param MovieNight    $movieNight movienight as parameter
     *
     * @return bool
     */
    private function validateMovieNightForm(FormInterface $form, MovieNight $movieNight): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            if ($movieNight->getDate() !== null && $movieNight->getDate()->format('Y.m.d') < date('Y.m.d')) {
                $this->addFlash('warning', 'Datum ist vergangen!');
            } elseif ($movieNight->getTime()
                && $movieNight->getDate()->format('d.m.Y') === date('d.m.Y')
                && $movieNight->getTime()->format('H:i') < date('H:i', time() - 900)) {
                $this->addFlash('warning', 'Zeitpunkt ist vergangen!');
            } else {
                return true;
            }
        }

        return false;
    }
}
