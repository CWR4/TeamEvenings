<?php


namespace App\Service;


use App\Entity\Voting;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VotingService extends AbstractController
{
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
     *  - creates array
     *  - array key => movie id
     *  - value => number of votes for movie
     */
    private function getVotes(Voting $voting)
    {
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