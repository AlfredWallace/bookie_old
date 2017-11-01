<?php
/**
 * Created by PhpStorm.
 * User: wallace
 * Date: 25/07/17
 * Time: 16:46
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Tournament;
use Doctrine\ORM\EntityNotFoundException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * @Rest\Route("/tournaments")
 *
 * Class TournamentController
 * @package AppBundle\Controller
 */
class TournamentController extends FOSRestController
{
    /**
     * @ApiDoc(
     *     resource=true,
     *     section="Tournaments",
     *     description="Lists all the tournaments.",
     *     statusCodes={200="Returned when the list is found."}
     * )
     *
     * @Rest\Get(name="app_tournament_list")
     * @Rest\View()
     *
     * @return Tournament[]|array
     */
    public function listTournamentsAction()
    {
        return $this->getDoctrine()->getManager()->getRepository('AppBundle:Tournament')->findAll();
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     section="Tournaments",
     *     description="Fetches a tournament.",
     *     statusCodes={200="Returned when the tournament is found."}
     * )
     *
     * @Rest\Get(
     *     path="/{id}",
     *     name="app_tournament_show",
     *     requirements={"id"="\d+"}
     * )
     * @Rest\View()
     *
     * @param Tournament $tournament
     * @return Tournament
     */
    public function showTournamentAction(Tournament $tournament)
    {
        return $tournament;
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     section="Tournaments",
     *     description="Removes a tournament and all matchEvents associated.",
     *     statusCodes={204="Returned when the tournament is deleted."}
     * )
     *
     * @Rest\Delete(
     *     path="/{id}",
     *     name="app_tournament_delete",
     *     requirements={"id"="\d+"}
     * )
     * @Rest\View(statusCode=204)
     *
     * @param Tournament $tournament
     * @return void
     */
    public function deleteTournamentAction(Tournament $tournament)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($tournament);
        $em->flush();
        return;
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     section="Tournaments",
     *     description="Creates a tournament.",
     *     statusCodes={
     *          201="Returned when created.",
     *          400="Returned when a violation is raised by validation."
     *     }
     * )
     *
     * @Rest\Post(name="app_tournament_create")
     * @Rest\View(statusCode=201)
     *
     * @ParamConverter(
     *     "tournament",
     *     converter="fos_rest.request_body",
     *     options={"validator"={"groups"="create"}}
     * )
     *
     * @param Tournament $tournament
     * @param ConstraintViolationList $violationList
     * @return Tournament|View
     */
    public function createTournamentAction(Tournament $tournament, ConstraintViolationList $violationList)
    {
        if (count($violationList)) {
            return $this->view($violationList, Response::HTTP_BAD_REQUEST);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($tournament);
        $em->flush();
        return $tournament;
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     section="Tournaments",
     *     description="Updates the title of a tournament.",
     *     statusCodes={
     *          204="Returned when the tournament is updated.",
     *          400="Returned when a violation is raised by validation."
     *     }
     * )
     *
     * @Rest\Put(
     *     path="/{id}",
     *     name="app_tournament_update",
     *     requirements={"id"="\d+"}
     * )
     * @Rest\View(statusCode=204)
     *
     * @ParamConverter(
     *     "tournament",
     *     converter="fos_rest.request_body",
     *     options={"validator"={"groups"="update"}}
     * )
     *
     * @param $id
     * @param Tournament $tournament
     * @param ConstraintViolationList $violationList
     * @return Tournament|View
     * @throws EntityNotFoundException
     */
    public function updateTournamentAction($id, Tournament $tournament, ConstraintViolationList $violationList)
    {
        if (count($violationList)) {
            return $this->view($violationList, Response::HTTP_BAD_REQUEST);
        }
        $em = $this->getDoctrine()->getManager();
        $databaseTournament = $em->getRepository('AppBundle:Tournament')->find($id);
        if (is_null($databaseTournament)) {
            throw new EntityNotFoundException('Tournament (id:'.$id.') not found.');
        }
        $databaseTournament->setTitle($tournament->getTitle());
        $em->flush();
        return $databaseTournament;
    }
}