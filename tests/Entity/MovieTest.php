<?php

namespace Tests\App\Entity;

use App\Entity\Movie;
use App\Entity\MovieNight;
use App\Entity\Voting;
use PHPUnit\Framework\TestCase;

/**
 * Class MovieTest
 */
class MovieTest extends TestCase
{
    /**
     * Test if title of movie can be set.
     */
    public function testCanSetAndGetTitle(): void
    {
        $movie = new Movie();
        $this->assertEmpty($movie->getTitle());
        $testTitle = 'Test123';
        $movie->setTitle($testTitle);
        $this->assertSame($testTitle, $movie->getTitle());
    }

    /**
     * Test if imdbId can be set and get.
     */
    public function testCanSetAndGetImdbId(): void
    {
        $movie = new Movie();
        $testImdbId = '123';
        $this->assertEmpty($movie->getImdbID());
        $movie->setImdbID($testImdbId);
        $this->assertSame($testImdbId, $movie->getImdbID());
    }

    /**
     * Test set / get of year.
     */
    public function testCanSetAndGetYear(): void
    {
        $movie = new Movie();
        $this->assertEmpty($movie->getYear());
        $testYear = '2019';
        $movie->setYear($testYear);
        $this->assertSame($testYear, $movie->getYear());
    }

    /**
     * Test set / get runtime.
     */
    public function testCanSetAndGetRuntime(): void
    {
        $movie = new Movie();
        $this->assertEmpty($movie->getRuntime());
        $testRuntime = '128 min';
        $movie->setRuntime($testRuntime);
        $this->assertSame($testRuntime, $movie->getRuntime());
    }

    /**
     * Test set / get poster.
     */
    public function testCanSetAndGetPoster(): void
    {
        $movie = new Movie();
        $this->assertEmpty($movie->getPoster());
        $testPoster = 'https://m.media-amazon.com/images/M/MV5BZWFlYmY2MGEtZjVkYS00YzU4LTg0YjQtYzY1ZGE3NTA5NGQxXkEyXkFqcGdeQXVyMTQxNzMzNDI@._V1_SX300.jpg';
        $movie->setPoster($testPoster);
        $this->assertSame($testPoster, $movie->getPoster());
    }

    /**
     * Test set / get plot.
     */
    public function testCanSetAndGetPlot(): void
    {
        $movie = new Movie();
        $this->assertEmpty($movie->getPlot());
        $testPlot = 'Lorem Ipsum dolorit...';
        $movie->setPlot($testPlot);
        $this->assertSame($testPlot, $movie->getPlot());
    }

    /**
     * Test if movienight can be added
     */
    public function testCanAddMovieNightToMovie(): void
    {
        $movie = new Movie();
        $this->assertEmpty($movie->getMovieNights());
        $movie->addMovieNight(new MovieNight());
        $this->assertCount(1, $movie->getMovieNights());
    }

    /**
     * Test if can remove movienight from movie.
     */
    public function testRemoveMovieNightFromMovie(): void
    {
        $movie = new Movie();
        $testMovieNight = new MovieNight();
        $movie->addMovieNight($testMovieNight);
        $this->assertCount(1, $movie->getMovieNights());
        $movie->removeMovieNight($testMovieNight);
        $this->assertEmpty($movie->getMovieNights());
    }

    /**
     * Test all voting methods.
     */
    public function testCanGetVotings(): void
    {
        $movie = new Movie();
        $testVoting = new Voting();
        $this->assertEmpty($movie->getVotings());
        $movie->addVoting($testVoting);
        $this->assertCount(1, $movie->getVotings());
        $movie->removeVoting($testVoting);
        $this->assertEmpty($movie->getVotings());
    }
}
