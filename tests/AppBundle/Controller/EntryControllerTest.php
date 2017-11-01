<?php
/**
 * Created by PhpStorm.
 * User: wallace
 * Date: 29/07/17
 * Time: 20:58
 */

namespace Tests\AppBundle\Controller;


use GuzzleHttp\Psr7\Request;
use Tests\AppBundle\ApiTestCase;

class EntryControllerTest extends ApiTestCase
{
    public static $entries = [
        'c1' => [
            'psg' => [
                'id' => null,
                'toEdit' => false,
                'toDelete' => false,
            ],
            'ars' => [
                'id' => null,
                'toEdit' => true,
                'toDelete' => false,
            ],
            'dor' => [
                'id' => null,
                'toEdit' => true,
                'toDelete' => false,
            ],
            'rma' => [
                'id' => null,
                'toEdit' => false,
                'toDelete' => false,
            ],
            'r92' => [
                'id' => null,
                'toEdit' => false,
                'toDelete' => true,
            ],
        ],
        'r1' => [
            'r92' => [
                'id' => null,
                'toEdit' => false,
                'toDelete' => false,
            ],
            'rct' => [
                'id' => null,
                'toEdit' => true,
                'toDelete' => false,
            ],
            'nsa' => [
                'id' => null,
                'toEdit' => false,
                'toDelete' => false,
            ],
            'lns' => [
                'id' => null,
                'toEdit' => true,
                'toDelete' => false,
            ],
            'psg' => [
                'id' => null,
                'toEdit' => false,
                'toDelete' => true,
            ],
        ],
    ];

    public function testCreateEntryAction()
    {
        foreach (self::$entries as $tournamentTitle => $teams) {
            $tournament = TournamentControllerTest::$tournaments[$tournamentTitle];
            $tournamentId = $tournament['id'];
            foreach ($teams as $teamName => $nothing) {
                $team = TeamControllerTest::$teams[$teamName];
                $teamId = $team['id'];
                $request = new Request('POST', '/entries', $this->requestConfig, json_encode([
                    'tournamentId' => $tournamentId,
                    'teamId' => $teamId,
                ]));
                $response = $this->client->send($request);
                $this->assertEquals(201,$response->getStatusCode());
                $body = json_decode((string) $response->getBody());
                $this->assertObjectHasAttribute('id',$body);

                $this->assertObjectHasAttribute('eliminated',$body);
                $this->assertAttributeEquals(false,'eliminated',$body);

                $this->assertObjectHasAttribute('team',$body);
                $this->assertObjectHasAttribute('id',$body->team);
                $this->assertAttributeEquals($teamId,'id',$body->team);
                $this->assertObjectHasAttribute('name',$body->team);
                $this->assertAttributeEquals($team['new'],'name',$body->team);

                $this->assertObjectHasAttribute('tournament',$body);
                $this->assertObjectHasAttribute('id',$body->tournament);
                $this->assertAttributeEquals($tournamentId,'id',$body->tournament);
                $this->assertObjectHasAttribute('title',$body->tournament);
                $this->assertAttributeEquals($tournament['new'],'title',$body->tournament);

                self::$entries[$tournamentTitle][$teamName]['id'] = $body->id;
            }
        }
    }

    /**
     * @depends testCreateEntryAction
     */
    public function testListEntriesAction()
    {
        $cpt = 0 ;
        foreach (self::$entries as $entry) {
            $cpt += count($entry);
        }
        $request = new Request('GET','/entries',$this->requestConfig);
        $response = $this->client->send($request);
        $this->assertEquals(200,$response->getStatusCode());
        $body = json_decode((string) $response->getBody());
        $this->assertCount($cpt,$body);
    }

    /**
     * @depends testCreateEntryAction
     */
    public function testShowEntryAction()
    {
        foreach (self::$entries as $tournamentTitle => $teams) {
            $tournament = TournamentControllerTest::$tournaments[$tournamentTitle];
            $tournamentId = $tournament['id'];
            foreach ($teams as $teamName => $data) {
                $team = TeamControllerTest::$teams[$teamName];
                $teamId = $team['id'];
                $request = new Request('GET','/entries/'.$data['id'], $this->requestConfig);
                $response = $this->client->send($request);
                $this->assertEquals(200,$response->getStatusCode());
                $body = json_decode((string) $response->getBody());
                $this->assertObjectHasAttribute('id',$body);
                $this->assertAttributeEquals($data['id'],'id',$body);

                $this->assertObjectHasAttribute('eliminated',$body);
                $this->assertAttributeEquals(false,'eliminated',$body);

                $this->assertObjectHasAttribute('team',$body);
                $this->assertObjectHasAttribute('id',$body->team);
                $this->assertAttributeEquals($teamId,'id',$body->team);
                $this->assertObjectHasAttribute('name',$body->team);
                $this->assertAttributeEquals($team['new'],'name',$body->team);

                $this->assertObjectHasAttribute('tournament',$body);
                $this->assertObjectHasAttribute('id',$body->tournament);
                $this->assertAttributeEquals($tournamentId,'id',$body->tournament);
                $this->assertObjectHasAttribute('title',$body->tournament);
                $this->assertAttributeEquals($tournament['new'],'title',$body->tournament);
            }
        }
    }

    /**
     * @depends testCreateEntryAction
     */
    public function testDeleteEntryAction()
    {
        foreach (self::$entries as $teams) {
            foreach ($teams as $data) {
                if ($data['toDelete']) {
                    $request = new Request('DELETE', '/entries/'.$data['id'], $this->requestConfig);
                    $response = $this->client->send($request);
                    $this->assertEquals(204, $response->getStatusCode());
                }
            }
        }
    }

    /**
     * @depends testCreateEntryAction
     */
    public function testUpdateEntryAction()
    {
        foreach (self::$entries as $tournamentTitle => $teams) {
            $tournament = TournamentControllerTest::$tournaments[$tournamentTitle];
            $tournamentId = $tournament['id'];
            foreach ($teams as $teamName => $data) {
                if ($data['toEdit']) {
                    $team = TeamControllerTest::$teams[$teamName];
                    $teamId = $team['id'];
                    $request = new Request('PUT', '/entries/'.$data['id'], $this->requestConfig, json_encode([
                        'eliminated' => true,
                        'tournamentId' => $tournamentId,
                        'teamId' => $teamId,
                    ]));
                    $response = $this->client->send($request);
                    $this->assertEquals(204,$response->getStatusCode());
                }
            }
        }
    }
}