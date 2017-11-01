<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * MatchEvent
 *
 * @ORM\Table(name="match_event")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MatchEventRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 *
 * @Hateoas\Relation(
 *     "self",
 *     href = @Hateoas\Route("app_matchevent_show", parameters={"id" = "expr(object.getId())"}, absolute=true)
 * )
 * @Hateoas\Relation(
 *     "create",
 *     href = @Hateoas\Route("app_matchevent_create", absolute=true)
 * )
 * @Hateoas\Relation(
 *     "update",
 *     href = @Hateoas\Route("app_matchevent_update", parameters={"id" = "expr(object.getId())"}, absolute=true)
 * )
 * @Hateoas\Relation(
 *     "delete",
 *     href = @Hateoas\Route("app_matchevent_delete", parameters={"id" = "expr(object.getId())"}, absolute=true)
 * )
 */
class MatchEvent
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
     * @var \DateTime
     *
     * @ORM\Column(name="kick_off", type="datetime", nullable=true)
     *
     * @Serializer\Expose()
     * @Serializer\Since("1.0")
     * @Serializer\SerializedName("kickOff")
     */
    protected $kickOff;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     *
     * @Assert\NotBlank(groups={"create","update"})
     *
     * @Serializer\Expose()
     * @Serializer\Since("1.0")
     */
    protected $title;

    /**
     * @var Tournament
     *
     * @ORM\ManyToOne(
     *     targetEntity="AppBundle\Entity\Tournament",
     *     inversedBy="matchEvents"
     * )
     *
     * @Assert\NotBlank(groups={"create","update"})
     *
     * @Serializer\Expose()
     * @Serializer\Since("1.0")
     */
    protected $tournament;

    /**
     * @var Team
     *
     * @ORM\ManyToOne(
     *     targetEntity="AppBundle\Entity\Team",
     *     inversedBy="matchEventsAsHome"
     * )
     *
     * @Assert\NotBlank(groups={"create","update"})
     *
     * @Serializer\Expose()
     * @Serializer\Since("1.0")
     * @Serializer\SerializedName("homeTeam")
     */
    protected $homeTeam;

    /**
     * @var Team
     *
     * @ORM\ManyToOne(
     *     targetEntity="AppBundle\Entity\Team",
     *     inversedBy="matchEventsAsAway"
     * )
     *
     * @Assert\NotBlank(groups={"create","update"})
     *
     * @Serializer\Expose()
     * @Serializer\Since("1.0")
     * @Serializer\SerializedName("awayTeam")
     */
    protected $awayTeam;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime|null
     */
    public function getKickOff()
    {
        return $this->kickOff;
    }

    /**
     * @param \DateTime $kickOff
     * @return MatchEvent
     */
    public function setKickOff(\DateTime $kickOff)
    {
        $this->kickOff = $kickOff;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return MatchEvent
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
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
     * @return MatchEvent
     */
    public function setTournament(Tournament $tournament)
    {
        $this->tournament = $tournament;
        return $this;
    }

    /**
     * @return Team
     */
    public function getHomeTeam(): Team
    {
        return $this->homeTeam;
    }

    /**
     * @param Team $homeTeam
     * @return MatchEvent
     */
    public function setHomeTeam(Team $homeTeam): MatchEvent
    {
        $this->homeTeam = $homeTeam;
        return $this;
    }

    /**
     * @return Team
     */
    public function getAwayTeam(): Team
    {
        return $this->awayTeam;
    }

    /**
     * @param Team $awayTeam
     * @return MatchEvent
     */
    public function setAwayTeam(Team $awayTeam): MatchEvent
    {
        $this->awayTeam = $awayTeam;
        return $this;
    }


}

