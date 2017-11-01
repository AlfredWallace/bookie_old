<?php
/**
 * Created by PhpStorm.
 * User: wallace
 * Date: 07/08/17
 * Time: 14:55
 */

namespace AppBundle\Factory;


use AppBundle\Entity\MatchEvent;
use AppBundle\Exception\InvalidArgumentException;
use AppBundle\Service\ArgumentChecker;

class MatchEventFactory
{
    private $argumentChecker;

    public function __construct(ArgumentChecker $argumentChecker)
    {
        $this->argumentChecker = $argumentChecker;
    }

    public function createMatchEvent(string $title, int $tournamentId, int $homeTeamId, int $awayTeamId, $kickOff)
    {
        $matchEvent = new MatchEvent();
        $this->hydrateMatchEvent($matchEvent, $title, $tournamentId, $homeTeamId, $awayTeamId, $kickOff);
        return $matchEvent;
    }

    public function hydrateMatchEvent(MatchEvent $matchEvent, string $title, int $tournamentId,
                                      int $homeTeamId, int $awayTeamId, $kickOff)
    {
        if ($homeTeamId == $awayTeamId) {
            throw new InvalidArgumentException(
                'Teams cannot be identical (home:'.$homeTeamId.', away:'.$awayTeamId.')'
            );
        }
        $this->argumentChecker->setIfEntryExists($matchEvent, $tournamentId, $homeTeamId, $awayTeamId);
        $matchEvent->setTitle($title);
        if (!is_null($kickOff)) {
            $kickOff = new \DateTime($kickOff);
            $matchEvent->setKickOff($kickOff);
        }
    }
}