<?php
/**
 * Created by PhpStorm.
 * User: wallace
 * Date: 07/08/17
 * Time: 10:44
 */

namespace AppBundle\Controller;


use AppBundle\Entity\MatchEvent;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Rest\Route("/match-events")
 *
 * Class MatchEventController
 * @package AppBundle\Controller
 */
class MatchEventController extends FOSRestController
{
    /**
     * @ApiDoc(
     *     resource=true,
     *     section="MatchEvents",
     *     description="List all the match events.",
     *     statusCodes={200="Returned when the list is found."}
     * )
     *
     * @Rest\Get(name="app_matchevent_list")
     * @Rest\View()
     *
     * @return MatchEvent[]|array
     */
    public function listMatchEventsAction()
    {
        return $this->getDoctrine()->getManager()->getRepository('AppBundle:MatchEvent')->findAll();
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     section="MatchEvents",
     *     description="Creates a new MatchEvent.",
     *     statusCodes={
     *          201="Returned when created.",
     *          400="Returned when a violation is raised by validation.",
     *          403="Returned when one of the teams has been eliminated.",
     *          404="Returned when an Entry for the tournament and one of the teams is not found."
     *     }
     * )
     *
     * @Rest\Post(name="app_matchevent_create")
     * @Rest\View(statusCode=201)
     * @Rest\RequestParam(
     *     name="title",
     *     nullable=false,
     *     allowBlank=false,
     *     description="The title of this match event (eg. Round of 16)."
     * )
     * @Rest\RequestParam(
     *     name="tournamentId",
     *     requirements="\d+",
     *     nullable=false,
     *     allowBlank=false,
     *     description="The id of the tournament in which the match event will be created."
     * )
     * @Rest\RequestParam(
     *     name="homeTeamId",
     *     requirements="\d+",
     *     nullable=false,
     *     allowBlank=false,
     *     description="The if of the home side team playing the match."
     * )
     * @Rest\RequestParam(
     *     name="awayTeamId",
     *     requirements="\d+",
     *     nullable=false,
     *     allowBlank=false,
     *     description="The if of the away side team playing the match."
     * )
     * @Rest\RequestParam(
     *     name="kickOff",
     *     requirements="\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])T([01]\d|2[0-3]):[0-5]\d:[0-5]\d[+-]([01]\d|2[0-3]):(00|15|30|45)",
     *     nullable=true,
     *     allowBlank=true,
     *     description="The date/time of the kick off"
     * )
     *
     * @param string $title
     * @param int $tournamentId
     * @param int $homeTeamId
     * @param int $awayTeamId
     * @param $kickOff
     * @return MatchEvent|View
     */
    public function createMatchEventAction(string $title, int $tournamentId, int $homeTeamId, int $awayTeamId, $kickOff)
    {
        $matchEventFactory = $this->get('app.factory.match_event');
        $matchEvent = $matchEventFactory->createMatchEvent($title, $tournamentId, $homeTeamId, $awayTeamId, $kickOff);
        $validator = $this->get('validator');
        $violationList = $validator->validate($matchEvent, null, ['create']);
        if (count($violationList)) {
            return $this->view($violationList, Response::HTTP_BAD_REQUEST);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($matchEvent);
        $em->flush();
        return $matchEvent;
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     section="MatchEvents",
     *     description="Fetches a MatchEvent.",
     *     statusCodes={200="Returned when the MatchEvent is found."}
     * )
     *
     * @Rest\Get(
     *     path="/{id}",
     *     name="app_matchevent_show",
     *     requirements={"id"="\d+"}
     * )
     * @Rest\View()
     *
     * @param MatchEvent $matchEvent
     * @return MatchEvent
     */
    public function showMatchEventAction(MatchEvent $matchEvent)
    {
        return $matchEvent;
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     section="MatchEvents",
     *     description="Deletes a MatchEvent.",
     *     statusCodes={204="Returned when the MatchEvent is deleted."}
     * )
     *
     * @Rest\Delete(
     *     path="/{id}",
     *     name="app_matchevent_delete",
     *     requirements={"id"="\d+"}
     * )
     * @Rest\View(statusCode=204)
     *
     * @param MatchEvent $matchEvent
     * @return void
     */
    public function deleteMatchEventAction(MatchEvent $matchEvent)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($matchEvent);
        $em->flush();
        return;
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     section="MatchEvents",
     *     description="Updates a MatchEvent.",
     *     statusCodes={
     *          201="Returned when created.",
     *          400="Returned when a violation is raised by validation.",
     *          403="Returned when one of the teams has been eliminated.",
     *          404="Returned when an Entry for the tournament and one of the teams is not found."
     *     }
     * )
     *
     * @Rest\Put(
     *     path="/{id}",
     *     name="app_matchevent_update",
     *     requirements={"id"="\d+"}
     * )
     * @Rest\View(statusCode=204)
     * @Rest\RequestParam(
     *     name="title",
     *     nullable=false,
     *     allowBlank=false,
     *     description="The title of this match event (eg. Round of 16)."
     * )
     * @Rest\RequestParam(
     *     name="tournamentId",
     *     requirements="\d+",
     *     nullable=false,
     *     allowBlank=false,
     *     description="The id of the tournament in which the match event will be created."
     * )
     * @Rest\RequestParam(
     *     name="homeTeamId",
     *     requirements="\d+",
     *     nullable=false,
     *     allowBlank=false,
     *     description="The if of the home side team playing the match."
     * )
     * @Rest\RequestParam(
     *     name="awayTeamId",
     *     requirements="\d+",
     *     nullable=false,
     *     allowBlank=false,
     *     description="The if of the away side team playing the match."
     * )
     * @Rest\RequestParam(
     *     name="kickOff",
     *     requirements="\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])T([01]\d|2[0-3]):[0-5]\d:[0-5]\d[+-]([01]\d|2[0-3]):(00|15|30|45)",
     *     nullable=true,
     *     allowBlank=true,
     *     description="The date/time of the kick off"
     * )
     * @param MatchEvent $matchEvent
     * @param string $title
     * @param int $tournamentId
     * @param int $homeTeamId
     * @param int $awayTeamId
     * @param $kickOff
     * @return MatchEvent|View
     */
    public function updateMatchEventAction(MatchEvent $matchEvent, string $title, int $tournamentId,
                                           int $homeTeamId, int $awayTeamId, $kickOff)
    {
        $matchEventFactory = $this->get('app.factory.match_event');
        $matchEventFactory->hydrateMatchEvent($matchEvent, $title, $tournamentId, $homeTeamId, $awayTeamId, $kickOff);

        $validator = $this->get('validator');
        $violationList = $validator->validate($matchEvent, null, ['update']);
        if (count($violationList)) {
            return $this->view($violationList, Response::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()->getManager();
        $em->flush();
        return $matchEvent;
    }
}