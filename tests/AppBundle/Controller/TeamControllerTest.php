<?php
/**
 * Created by PhpStorm.
 * User: wallace
 * Date: 29/07/17
 * Time: 17:19
 */

namespace Tests\AppBundle\Controller;


use GuzzleHttp\Psr7\Request;
use Tests\AppBundle\ApiTestCase;

class TeamControllerTest extends ApiTestCase
{
    public static $teams = [
        'r92' => [
            'id' => null,
            'old' => 'Racing-Métro 92',
            'new' => 'Racing 92',
            'toEdit' => true,
            'toDelete' => false,
            'toEliminate' => false,
        ],
        'rct' => [
            'id' => null,
            'old' => 'Racing Club Toulonnais',
            'new' => 'RC Toulon',
            'toEdit' => true,
            'toDelete' => false,
            'toEliminate' => true,
        ],
        'nsa' => [
            'id' => null,
            'old' => 'Northampton Saints',
            'new' => 'Northampton Saints',
            'toEdit' => false,
            'toDelete' => false,
            'toEliminate' => false,
        ],
        'lns' => [
            'id' => null,
            'old' => 'Leinster',
            'new' => 'Leinster',
            'toEdit' => false,
            'toDelete' => false,
            'toEliminate' => true,
        ],
        'psg' => [
            'id' => null,
            'old' => 'Paris Saint-Germain',
            'new' => 'Paris Saint-Germain',
            'toEdit' => false,
            'toDelete' => false,
            'toEliminate' => false,
        ],
        'ars' => [
            'id' => null,
            'old' => 'Arsenal',
            'new' => 'Arsenal',
            'toEdit' => false,
            'toDelete' => false,
            'toEliminate' => true,
        ],
        'dor' => [
            'id' => null,
            'old' => 'Dortmund',
            'new' => 'Dortmund',
            'toEdit' => false,
            'toDelete' => false,
            'toEliminate' => true,
        ],
        'rma' => [
            'id' => null,
            'old' => 'Real Madrid',
            'new' => 'Real Madrid',
            'toEdit' => false,
            'toDelete' => false,
            'toEliminate' => false,
        ],
        'qev' => [
            'id' => null,
            'old' => 'Quevilly',
            'new' => 'Quevilly',
            'toEdit' => false,
            'toDelete' => true,
            'toEliminate' => false,
        ],
    ];

    public function testCreateTeamAction()
    {

        foreach (self::$teams as $key => $team) {
            $request = new Request('POST', '/teams', $this->requestConfig, json_encode([
                'name' => $team['old'],
            ]));
            $response = $this->client->send($request);
            $this->assertEquals(201,$response->getStatusCode());
            $body = json_decode((string) $response->getBody());
            $this->assertObjectHasAttribute('id',$body);
            $this->assertObjectHasAttribute('name',$body);
            $this->assertAttributeEquals($team['old'],'name',$body);
            self::$teams[$key]['id'] = $body->id;
        }
    }

    /**
     * @depends testCreateTeamAction
     */
    public function testListTeamsAction()
    {
        $request = new Request('GET','/teams',$this->requestConfig);
        $response = $this->client->send($request);
        $this->assertEquals(200,$response->getStatusCode());
        $body = json_decode((string) $response->getBody());
        $this->assertCount(count(self::$teams),$body);
    }

    /**
     * @depends testCreateTeamAction
     */
    public function testShowTeamAction()
    {
        foreach (self::$teams as $key => $team) {
            $id = $team['id'];
            $request = new Request('GET','/teams/'.$id, $this->requestConfig);
            $response = $this->client->send($request);
            $this->assertEquals(200,$response->getStatusCode());
            $body = json_decode((string) $response->getBody());
            $this->assertObjectHasAttribute('id',$body);
            $this->assertAttributeEquals($id,'id',$body);
            $this->assertObjectHasAttribute('name',$body);
            $this->assertAttributeEquals($team['old'],'name',$body);
        }
    }

    /**
     * @depends testCreateTeamAction
     */
    public function testUpdateTeamAction()
    {
        foreach (self::$teams as $team) {
            if ($team['toEdit']) {
                $request = new Request('PUT', '/teams/' . $team['id'], $this->requestConfig, json_encode([
                    'name' => $team['new'],
                ]));
                $response = $this->client->send($request);
                $this->assertEquals(204, $response->getStatusCode());
            }
        }
    }

    /**
     * @depends testCreateTeamAction
     */
    public function testDeleteTeamAction()
    {
        foreach (self::$teams as $team) {
            if ($team['toDelete']) {
                $request = new Request('DELETE', '/teams/'.$team['id'], $this->requestConfig);
                $response = $this->client->send($request);
                $this->assertEquals(204, $response->getStatusCode());
            }
        }
    }
}