<?php
/**
 * Created by PhpStorm.
 * User: wallace
 * Date: 29/07/17
 * Time: 21:09
 */

namespace AppBundle\Factory;


use AppBundle\Entity\Entry;
use AppBundle\Service\ArgumentChecker;

//TODO: use the services autowire
class EntryFactory
{
    private $argumentChecker;

    public function __construct(ArgumentChecker $argumentChecker)
    {
        $this->argumentChecker = $argumentChecker;
    }

    public function createEntry(int $tournamentId, int $teamId, bool $eliminated)
    {
        $entry = new Entry();
        $this->hydrateEntry($entry, $tournamentId, $teamId, $eliminated);
        return $entry;
    }

    public function hydrateEntry(Entry $entry, int $tournamentId, int $teamId, bool $eliminated)
    {
        $this->argumentChecker->setIfExists($entry, $tournamentId,'tournament');
        $this->argumentChecker->setIfExists($entry, $teamId,'team');
        $entry->setEliminated($eliminated);
    }
}