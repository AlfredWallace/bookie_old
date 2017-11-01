<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * Entry
 *
 * @ORM\Table(
 *     name="entry",
 *     uniqueConstraints={@ORM\UniqueConstraint(columns={"team_id","tournament_id"})}
 * )
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntryRepository")
 * @UniqueEntity(
 *     fields={"team","tournament"},
 *     groups={"create","update"},
 *     message="This team has already entered this tournament."
 * )
 *
 * @Serializer\ExclusionPolicy("all")
 *
 * @Hateoas\Relation(
 *     "self",
 *     href = @Hateoas\Route("app_entry_show", parameters={"id" = "expr(object.getId())"}, absolute=true)
 * )
 * @Hateoas\Relation(
 *     "create",
 *     href = @Hateoas\Route("app_entry_create", absolute=true)
 * )
 * @Hateoas\Relation(
 *     "update",
 *     href = @Hateoas\Route("app_entry_update", parameters={"id" = "expr(object.getId())"}, absolute=true)
 * )
 * @Hateoas\Relation(
 *     "delete",
 *     href = @Hateoas\Route("app_entry_delete", parameters={"id" = "expr(object.getId())"}, absolute=true)
 * )
 */
class Entry
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Serializer\Expose()
     * @Serializer\Since("1.0")
     */
    protected $id;

    /**
     * @var bool
     *
     * @ORM\Column(name="eliminated", type="boolean")
     *
     * @Assert\Type(
     *     type="bool",
     *     groups={"create","update"}
     * )
     *
     * @Serializer\Expose()
     * @Serializer\Since("1.0")
     */
    protected $eliminated;

    /**
     * @var Team
     *
     * @ORM\ManyToOne(
     *     targetEntity="AppBundle\Entity\Team",
     *     inversedBy="entries"
     * )
     *
     * @Assert\NotBlank(groups={"create","update"})
     *
     * @Serializer\Expose()
     * @Serializer\Since("1.0")
     */
    protected $team;

    /**
     * @var Tournament
     *
     * @ORM\ManyToOne(
     *     targetEntity="AppBundle\Entity\Tournament",
     *     inversedBy="entries"
     * )
     *
     * @Assert\NotBlank(groups={"create","update"})
     *
     * @Serializer\Expose()
     * @Serializer\Since("1.0")
     */
    protected $tournament;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set eliminated
     *
     * @param boolean $eliminated
     *
     * @return Entry
     */
    public function setEliminated(bool $eliminated): Entry
    {
        $this->eliminated = $eliminated;

        return $this;
    }

    /**
     * Get eliminated
     *
     * @return bool
     */
    public function getEliminated()
    {
        return $this->eliminated;
    }

    /**
     * @return Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @param Team $team
     * @return Entry
     */
    public function setTeam(Team $team): Entry
    {
        $this->team = $team;
        return $this;
    }

    /**
     * @return Tournament
     */
    public function getTournament()
    {
        return $this->tournament;
    }

    /**
     * @param Tournament $tournament
     * @return Entry
     */
    public function setTournament(Tournament $tournament): Entry
    {
        $this->tournament = $tournament;
        return $this;
    }
}

