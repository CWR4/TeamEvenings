<?php


namespace App\Service;


use App\Entity\Vote;
use App\Entity\Voting;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VotingService extends AbstractController
{
    /*
     *  -
     */
    public function getVotingResult($votingId)
    {
        // Get voting by id
        $result['voting'] = $this->getDoctrine()->getManager()->getRepository(Voting::class)->getVoting($votingId);

        if($result['voting'])
        {
            $result['votes'] = $this->getVotes($result['voting']);
        }
        else
        {
            return null;
        }

        return $result;
    }

    /*
     *  - vote
     */
    public function vote(Voting $voting, $mid) : void
    {
        if(!$this->hasVoted($voting->getVotes()))
        {
            foreach($voting->getMovies() as $movie)
            {
                dump($movie);
                if($movie->getId() ===(int)$mid)
                {
                    $vote = new Vote();
                    $vote->setUser($this->getUser());
                    $vote->setMovie($movie);
                    $vote->setVoting($voting);

                    $manager = $this->getDoctrine()->getManager();
                    $manager->persist($vote);
                    $manager->flush();

                    $this->addFlash('success', 'Erfolgreich abgestimmt!');
                }
            }
        }
    }

    /*
     *  - checks if user already voted and add flash message
     */
    private function hasVoted($votes) : bool
    {
        $user = $this->getUser();

        if($votes)
        {
            foreach($votes as $vote)
            {
                if($user === $vote->getUser())
                {
                    $this->addFlash('warning', 'Sie haben bereits abgestimmt');
                    return true;
                }
            }
        }

        return false;
    }

    /*
     *  - creates array
     *  - array key => movie id
     *  - value => number of votes for movie
     */
    private function getVotes(Voting $voting)
    {
        $votes = [];

        foreach ($voting->getMovies() as $movie)
        {
            $votes[$movie->getId()] = 0;
        }

        foreach ($voting->getVotes() as $vote)
        {
            $votes[$vote->getMovie()->getId()]++;
        }

        return $votes;
    }
}