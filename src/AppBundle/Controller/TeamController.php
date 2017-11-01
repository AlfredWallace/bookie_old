<?php
/**
 * Created by PhpStorm.
 * User: wallace
 * Date: 27/07/17
 * Time: 23:15
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Team;
use Doctrine\ORM\EntityNotFoundException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * @Rest\Route("/teams")
 *
 * Class TeamController
 * @package AppBundle\Controller
 */
class TeamController extends FOSRestController
{
    /**
     * @ApiDoc(
     *     resource=true,
     *     section="Teams",
     *     description="Lists all the teams.",
     *     statusCodes={200="Returned when the list is found."}
     * )
     *
     * @Rest\Get(name="app_team_list")
     * @Rest\View()
     *
     * @return Team[]|array
     */
    public function listTeamsAction()
    {
        return $this->getDoctrine()->getManager()->getRepository('AppBundle:Team')->findAll();
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     section="Teams",
     *     description="Creates a team.",
     *     statusCodes={
     *          201="Returned when created.",
     *          400="Returned when a violation is raised by validation."
     *     }
     * )
     *
     * @Rest\Post(name="app_team_create")
     * @Rest\View(statusCode=201)
     *
     * @ParamConverter(
     *     "team",
     *     converter="fos_rest.request_body",
     *     options={"validator"={"groups"="create"}}
     * )
     *
     * @param Team $team
     * @param ConstraintViolationList $violationList
     * @return Team|View
     */
    public function createTeamAction(Team $team, ConstraintViolationList $violationList)
    {
        if (count($violationList)) {
            return $this->view($violationList, Response::HTTP_BAD_REQUEST);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($team);
        $em->flush();
        return $team;
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     section="Teams",
     *     description="Fetches a team.",
     *     statusCodes={200="Returned when the team is found"}
     * )
     *
     * @Rest\Get(
     *     path="/{id}",
     *     name="app_team_show",
     *     requirements={"id"="\d+"}
     * )
     * @Rest\View()
     *
     * @param Team $team
     * @return Team
     */
    public function showTeamAction(Team $team)
    {
        return $team;
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     section="Teams",
     *     description="Deletes a team and all matchEvents associated",
     *     statusCodes={204="Returned when the team is deleted."}
     * )
     *
     * @Rest\Delete(
     *     path="/{id}",
     *     name="app_team_delete",
     *     requirements={"id"="\d+"}
     * )
     * @Rest\View(statusCode=204)
     *
     * @param Team $team
     * @return void
     */
    public function deleteTeamAction(Team $team)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($team);
        $em->flush();
        return;
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     section="Teams",
     *     description="Updates a team.",
     *     statusCodes={
     *          204="Returned when the team is updated.",
     *          400="Returned when a violation is raised by validation.",
     *          404="Returned when a team is not found."
     *     }
     * )
     *
     * @Rest\Put(
     *     path="/{id}",
     *     name="app_team_update",
     *     requirements={"id"="\d+"}
     * )
     * @Rest\View(statusCode=204)
     *
     * @ParamConverter(
     *     "team",
     *     converter="fos_rest.request_body",
     *     options={"validator"={"groups"="update"}}
     * )
     *
     * @param $id
     * @param Team $team
     * @param ConstraintViolationList $violationList
     * @return Team|View
     * @throws EntityNotFoundException
     */
    public function updateTeamAction($id, Team $team, ConstraintViolationList $violationList)
    {
        if (count($violationList)) {
            return $this->view($violationList, Response::HTTP_BAD_REQUEST);
        }
        $em = $this->getDoctrine()->getManager();
        $databaseTeam = $em->getRepository('AppBundle:Team')->find($id);
        if (is_null($databaseTeam)) {
            throw new EntityNotFoundException('Tournament (id:'.$id.') not found.');
        }
        $databaseTeam->setName($team->getName());
        $em->flush();
        return $databaseTeam;
    }
}