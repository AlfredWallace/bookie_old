<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Team
 *
 * @ORM\Table(name="team")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TeamRepository")
 * @UniqueEntity(
 *     fields={"name"},
 *     groups={"create","update"}
 * )
 *
 * @Serializer\ExclusionPolicy("all")
 * 
 * @Hateoas\Relation(
 *     "self",
 *     href = @Hateoas\Route("app_team_show", parameters={"id" = "expr(object.getId())"}, absolute=true)
 * )
 * @Hateoas\Relation(
 *     "create",
 *     href = @Hateoas\Route("app_team_create", absolute=true)
 * )
 * @Hateoas\Relation(
 *     "update",
 *     href = @Hateoas\Route("app_team_update", parameters={"id" = "expr(object.getId())"}, absolute=true)
 * )
 * @Hateoas\Relation(
 *     "delete",
 *     href = @Hateoas\Route("app_team_delete", parameters={"id" = "expr(object.getId())"}, absolute=true)
 * )
 */
class Team
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     *
     * @Assert\NotBlank(groups={"create","update"})
     *
     * @Serializer\Expose()
     * @Serializer\Since("1.0")
     */
    protected $name;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="AppBundle\Entity\Entry",
     *     mappedBy="team",
     *     cascade={"remove"}
     * )
     */
    protected $entries;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="AppBundle\Entity\MatchEvent",
     *     mappedBy="homeTeam",
     *     cascade={"remove"}
     * )
     */
    protected $matchEventsAsHome;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="AppBundle\Entity\MatchEvent",
     *     mappedBy="awayTeam",
     *     cascade={"remove"}
     * )
     */
    protected $matchEventsAsAway;

    /**
     * Team constructor.
     */
    public function __construct()
    {
        $this->entries = new ArrayCollection();
        $this->matchEventsAsHome = new ArrayCollection();
        $this->matchEventsAsAway = new ArrayCollection();
    }

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
     * Set name
     *
     * @param string $name
     *
     * @return Team
     */
    public function setName(string $name): Team
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}

