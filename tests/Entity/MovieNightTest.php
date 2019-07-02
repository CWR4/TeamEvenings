<?php

namespace App\Entity;

use PHPUnit\Framework\TestCase;
use DateTime;

/**
 * Class MovieNightTest
 */
class MovieNightTest extends TestCase
{
    /**
     * Test set / get of date.
     */
    public function testCanSetAndGetDate(): void
    {
        $movieNight = new MovieNight();
        $this->assertEmpty($movieNight->getDate());
        $testDate = new DateTime();
        $testDate->format('Y-m-d');
        $movieNight->setDate($testDate);
        $this->assertSame($testDate, $movieNight->getDate());
    }

    /**
     * Test set / get of time.
     */
    public function testCanSetAndGetTime(): void
    {
        $movieNight = new MovieNight();
        $this->assertEmpty($movieNight->getTime());
        $testTime = new DateTime();
        $testTime->format('h:i');
        $movieNight->setTime($testTime);
        $this->assertSame($testTime, $movieNight->getTime());
    }

    /**
     * Test set / get of location.
     */
    public function testCanSetAndGetLocation(): void
    {
        $movieNight = new MovieNight();
        $this->assertEmpty($movieNight->getLocation());
        $testLocation = 'K. 56 - 5. OG';
        $movieNight->setLocation($testLocation);
        $this->assertSame($testLocation, $movieNight->getLocation());
    }

    /**
     * Test set / get of movie.
     */
    public function testCanSetAndGetMovie(): void
    {
        $movieNight = new MovieNight();
        $this->assertEmpty($movieNight->getMovie());
        $testMovie = new Movie();
        $movieNight->setMovie($testMovie);
        $this->assertSame($testMovie, $movieNight->getMovie());
    }

    /**
     * Test set / get for voting.
     */
    public function testCanSetAndGetVoting(): void
    {
        $movieNight = new MovieNight();
        $this->assertEmpty($movieNight->getVoting());
        $testVoting = new Voting();
        $movieNight->setVoting($testVoting);
        $this->assertSame($testVoting, $movieNight->getVoting());
    }
}
