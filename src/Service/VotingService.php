<?php

namespace App\Service;

use App\Entity\Movie;
use App\Entity\MovieNight;
use App\Entity\Vote;

use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class VotingService
 */
class VotingService extends AbstractController
{
    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * VotingService constructor.
     *
     * @param ObjectManager $manager to handle orm entities
     */
    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param MovieNight $movieNight movienight
     * @param Movie      $movie      movie
     */
    public function vote(MovieNight $movieNight, Movie $movie): void
    {
        if ($movieNight && !$this->hasVoted($movieNight->getVotes())) {
            $vote = new Vote();
            $vote->setUser($this->getUser());
            $vote->setMovie($movie);
            $vote->setMovieNight($movieNight);

            $this->manager->persist($vote);
            $this->manager->flush();

            $this->addFlash('success', 'Erfolgreich abgestimmt!');
        }
    }

    /**
     *  - delete all votes in given movienight for given movie
     *  - in case a movie gets removed from voting
     *
     * @param MovieNight $movieNight as parameter
     * @param Movie      $movie      delete votes for movie in voting
     */
    public function deleteVotes(MovieNight $movieNight, Movie $movie): void
    {
        $votes = $movieNight->getVotes();

        foreach ($votes as $vote) {
            if ($vote->getMovie() === $movie) {
                $movieNight->removeVote($vote);
            }
        }
    }

    /**
     * @param MovieNight $movieNight movienight
     *
     * @return array
     */
    public function getVotes(MovieNight $movieNight): array
    {
        $votes = [];

        foreach ($movieNight->getMovies() as $movie) {
            $votes[$movie->getId()] = 0;
        }

        foreach ($movieNight->getVotes() as $vote) {
            ++$votes[$vote->getMovie()->getId()];
        }

        return $votes;
    }

    /**
     * @param array|null $votes array with all votes of voting
     *
     * @return bool
     */
    private function hasVoted($votes): bool
    {
        $user = $this->getUser();

        if ($votes) {
            foreach ($votes as $vote) {
                if ($user === $vote->getUser()) {
                    $this->addFlash('warning', 'Sie haben bereits abgestimmt');

                    return true;
                }
            }
        }

        return false;
    }
}
