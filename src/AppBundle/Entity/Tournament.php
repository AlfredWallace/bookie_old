<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Tournament
 *
 * @ORM\Table(name="tournament")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TournamentRepository")
 * @UniqueEntity(
 *     fields={"title"},
 *     groups={"create","update"}
 * )
 *
 * @Serializer\ExclusionPolicy("all")
 *
 * @Hateoas\Relation(
 *     "self",
 *     href = @Hateoas\Route("app_tournament_show", parameters={"id" = "expr(object.getId())"}, absolute=true)
 * )
 * @Hateoas\Relation(
 *     "create",
 *     href = @Hateoas\Route("app_tournament_create", absolute=true)
 * )
 * @Hateoas\Relation(
 *     "update",
 *     href = @Hateoas\Route("app_tournament_update", parameters={"id" = "expr(object.getId())"}, absolute=true)
 * )
 * @Hateoas\Relation(
 *     "delete",
 *     href = @Hateoas\Route("app_tournament_delete", parameters={"id" = "expr(object.getId())"}, absolute=true)
 * )
 */
class Tournament
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
     * @ORM\Column(name="title", type="string", length=255, unique=true)
     *
     * @Assert\NotBlank(groups={"create","update"})
     *
     * @Serializer\Expose()
     * @Serializer\Since("1.0")
     */
    protected $title;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="AppBundle\Entity\Entry",
     *     mappedBy="tournament",
     *     cascade={"remove"}
     * )
     */
    protected $entries;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="AppBundle\Entity\MatchEvent",
     *     mappedBy="tournament",
     *     cascade={"remove"}
     * )
     */
    protected $matchEvents;

    public function __construct()
    {
        $this->entries = new ArrayCollection();
        $this->matchEvents = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @return Tournament
     */
    public function setTitle(string $title): Tournament
    {
        $this->title = $title;
        return $this;
    }
}

