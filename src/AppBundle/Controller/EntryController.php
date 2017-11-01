<?php
/**
 * Created by PhpStorm.
 * User: wallace
 * Date: 28/07/17
 * Time: 12:19
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Entry;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Response;

//TODO: use the services autowire
/**
 * @Rest\Route("/entries")
 * 
 * Class EntryController
 * @package AppBundle\Controller
 */
class EntryController extends FOSRestController
{
    /**
     * @ApiDoc(
     *     resource=true,
     *     section="Entries",
     *     description="Lists all the entries.",
     *     statusCodes={200="Returned when the list is found."}
     * )
     *
     * @Rest\Get(name="app_entry_list")
     * @Rest\View()
     *
     * @return Entry[]|array
     */
    public function listEntriesAction()
    {
        return $this->getDoctrine()->getManager()->getRepository('AppBundle:Entry')->findAll();
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     section="Entries",
     *     description="Creates a new entry.",
     *     statusCodes={
     *          201="Returned when created.",
     *          400="Returned when a violation is raised by validation.",
     *          404="Returned when a team or a tournament is not found."
     *     }
     * )
     *
     * @Rest\Post(name="app_entry_create")
     * @Rest\View(statusCode=201)
     * @Rest\RequestParam(
     *     name="tournamentId",
     *     requirements="\d+",
     *     nullable=false,
     *     allowBlank=false,
     *     description="The id of the tournament in which the team enters."
     * )
     * @Rest\RequestParam(
     *     name="teamId",
     *     requirements="\d+",
     *     nullable=false,
     *     allowBlank=false,
     *     description="The id of the team entering the tournament."
     * )
     * @Rest\RequestParam(
     *     name="eliminated",
     *     requirements="[01]",
     *     nullable=true,
     *     allowBlank=true,
     *     default=0,
     *     description="The status (eliminated or not) of the team."
     * )
     *
     * @param $tournamentId
     * @param $teamId
     * @param $eliminated
     * @return Entry|View
     */
    public function createEntryAction(int $tournamentId, int $teamId, $eliminated)
    {
        $entryFactory = $this->get('app.factory.entry');
        $entry = $entryFactory->createEntry($tournamentId, $teamId, $eliminated);
        $validator = $this->get('validator');
        $violationList = $validator->validate($entry,null,['create']);
        if (count($violationList)) {
            return $this->view($violationList, Response::HTTP_BAD_REQUEST);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($entry);
        $em->flush();
        return $entry;
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     section="Entries",
     *     description="Fetches an entry.",
     *     statusCodes={200="Returned when the entry is found."}
     * )
     *
     * @Rest\Get(
     *     path="/{id}",
     *     name="app_entry_show",
     *     requirements={"id"="\d+"}
     * )
     * @Rest\View()
     *
     * @param Entry $entry
     * @return Entry
     */
    public function showEntryAction(Entry $entry)
    {
        return $entry;
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     section="Entries",
     *     description="Deletes an entry.",
     *     statusCodes={204="Returned when the entry is deleted."}
     * )
     *
     * @Rest\Delete(
     *     path="/{id}",
     *     name="app_entry_delete",
     *     requirements={"id"="\d+"}
     * )
     * @Rest\View(statusCode=204)
     *
     * @param Entry $entry
     * @return void
     */
    public function deleteEntryAction(Entry $entry)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($entry);
        $em->flush();
        return;
    }


    /**
     * @ApiDoc(
     *     resource=true,
     *     section="Entries",
     *     description="Updates an Entry.",
     *     statusCodes={
     *          201="Returned when updated.",
     *          400="Returned when a violation is raised by validation.",
     *          404="Returned when a team or a tournament is not found."
     *     }
     * )
     *
     * @Rest\Put(
     *     path="/{id}",
     *     name="app_entry_update",
     *     requirements={"id"="\d+"}
     * )
     * @Rest\View(statusCode=204)
     * @Rest\RequestParam(
     *     name="tournamentId",
     *     requirements="\d+",
     *     nullable=false,
     *     allowBlank=false,
     *     description="The id of the tournament in which the team enters."
     * )
     * @Rest\RequestParam(
     *     name="teamId",
     *     requirements="\d+",
     *     nullable=false,
     *     allowBlank=false,
     *     description="The id of the team entering the tournament."
     * )
     * @Rest\RequestParam(
     *     name="eliminated",
     *     requirements="[01]",
     *     nullable=false,
     *     allowBlank=false,
     *     description="The status (eliminated or not) of the team."
     * )
     *
     * @param Entry $entry
     * @param $tournamentId
     * @param $teamId
     * @param $eliminated
     * @return Entry|View
     */
    public function updateEntryAction(Entry $entry, int $tournamentId, int $teamId, $eliminated)
    {
        $entryFactory = $this->get('app.factory.entry');
        $entryFactory->hydrateEntry($entry, $tournamentId, $teamId, $eliminated);

        $validator = $this->get('validator');
        $violationList = $validator->validate($entry,null,['update']);
        if (count($violationList)) {
            return $this->view($violationList, Response::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()->getManager();
        $em->flush();
        return $entry;
    }
}