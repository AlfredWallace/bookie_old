<?php
/**
 * Created by PhpStorm.
 * User: wallace
 * Date: 31/07/17
 * Time: 00:34
 */

namespace AppBundle\Service;


use AppBundle\Entity\MatchEvent;
use AppBundle\Exception\InvalidArgumentException;
use AppBundle\Exception\MethodNotFoundException;
use AppBundle\Exception\TeamEliminatedException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;

//TODO: use the services autowire
class ArgumentChecker
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param int $number
     * @param string|null $label
     * @return bool
     * @throws InvalidArgumentException
     */
    public function checkIfPositive(int $number, string $label = null)
    {
        if ($number <= 0) {
            $str = $number;
            if (!is_null($label)) {
                $str.= ' ('.$label.')';
            }
            $str.= ' is not a valid id (must be a strictly positive integer).';
            throw new InvalidArgumentException($str);
        }
        return true;
    }

    /**
     * @param mixed $object
     * @param int $id
     * @param string $className
     * @throws EntityNotFoundException
     * @throws InvalidArgumentException
     * @throws MethodNotFoundException
     */
    public function setIfExists($object, int $id, string $className)
    {
        $this->checkIfPositive($id);

        $className = ucfirst(strtolower($className));
        $setter = 'set'.$className;

        if (!method_exists($object,$setter)) {
            throw new MethodNotFoundException('Method '.$setter.' not found in class '.get_class($object));
        }

        $databaseArgument = $this->em->getRepository('AppBundle:'.$className)->find($id);

        if (is_null($databaseArgument)) {
            throw new EntityNotFoundException($className.' (id:'.$id.') not found.');
        }

        $object->$setter($databaseArgument);
    }

    /**
     * @param MatchEvent $matchEvent
     * @param int $tournamentId
     * @param int $homeTeamId
     * @param int $awayTeamId
     * @throws EntityNotFoundException
     * @throws InvalidArgumentException
     * @throws TeamEliminatedException
     */
    public function setIfEntryExists(MatchEvent $matchEvent, int $tournamentId, int $homeTeamId, int $awayTeamId)
    {
        $ids = [
            'tournamentId' => $tournamentId,
            'homeTeamId' => $homeTeamId,
            'awayTeamId' => $awayTeamId
        ];
        foreach ($ids as $label => $id) {
            $this->checkIfPositive($id,$label);
        }

        /**
         * We only check for the existence of an Entry, and not for the Tournament or the Teams because of the
         * cascade remove and the unique constraints, cf: the three entities.
         */
        unset($ids['tournamentId']);
        $entryRepo = $this->em->getRepository('AppBundle:Entry');
        foreach ($ids as $id) {
            $entry = $entryRepo->findOneBy(['tournament' => $tournamentId, 'team' => $id]);
            if (is_null($entry)) {
                throw new EntityNotFoundException(
                    'Entry (tournamentId: '.$tournamentId.' ; teamId:'.$id.') not found.'
                );
            }
            if ($entry->getEliminated() == true) {
                throw new TeamEliminatedException(
                    'This team (id: '.$id.') has been eliminated from this tournament (id:'.$tournamentId.').'
                );
            }
        }
        $teamRepo = $this->em->getRepository('AppBundle:Team');
        $matchEvent
            ->setTournament($this->em->getRepository('AppBundle:Tournament')->find($tournamentId))
            ->setHomeTeam($teamRepo->find($homeTeamId))
            ->setAwayTeam($teamRepo->find($awayTeamId))
        ;
    }
}