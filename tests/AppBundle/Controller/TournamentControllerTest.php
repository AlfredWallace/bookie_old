<?php
/**
 * Created by PhpStorm.
 * User: wallace
 * Date: 26/07/17
 * Time: 11:02
 */

namespace Tests\AppBundle\Controller;

use GuzzleHttp\Psr7\Request;
use Tests\AppBundle\ApiTestCase;

class TournamentControllerTest extends ApiTestCase
{
    public static $tournaments = [
        'c1' => [
            'id' => null,
            'old' => 'Coupe des clubs champions',
            'new' => 'Champions League',
            'toEdit' => true,
            'toDelete' => false,
        ],
        'r1' => [
            'id' => null,
            'old' => 'H Cup',
            'new' => 'Rugby Champions Cup',
            'toEdit' => true,
            'toDelete' => false,
        ],
        'wc' => [
            'id' => null,
            'old' => 'Coupe du monde',
            'new' => 'Coupe du monde',
            'toEdit' => false,
            'toDelete' => true,
        ],
    ];

    public function testCreateTournamentAction()
    {
        foreach (self::$tournaments as $key => $tournament) {
            $request = new Request('POST', '/tournaments', $this->requestConfig,json_encode([
                'title' => $tournament['old'],
            ]));
            $response = $this->client->send($request);
            $this->assertEquals(201,$response->getStatusCode());
            $body = json_decode((string) $response->getBody());
            $this->assertObjectHasAttribute('id',$body);
            $this->assertObjectHasAttribute('title',$body);
            $this->assertAttributeEquals($tournament['old'],'title',$body);
            self::$tournaments[$key]['id'] = $body->id;
        }
    }

    /**
     * @depends testCreateTournamentAction
     */
    public function testListTournamentsAction()
    {
        $request = new Request('GET','/tournaments',$this->requestConfig);
        $response = $this->client->send($request);
        $this->assertEquals(200,$response->getStatusCode());
        $body = json_decode((string) $response->getBody());
        $this->assertCount(count(self::$tournaments),$body);
    }

    /**
     * @depends testCreateTournamentAction
     */
    public function testShowTournamentAction()
    {
        foreach (self::$tournaments as $key => $tournament) {
            $id = $tournament['id'];
            $request = new Request('GET','/tournaments/'.$id, $this->requestConfig);
            $response = $this->client->send($request);
            $this->assertEquals(200,$response->getStatusCode());
            $body = json_decode((string) $response->getBody());
            $this->assertObjectHasAttribute('id',$body);
            $this->assertAttributeEquals($id,'id',$body);
            $this->assertObjectHasAttribute('title',$body);
            $this->assertAttributeEquals($tournament['old'],'title',$body);
        }
    }

    /**
     * @depends testCreateTournamentAction
     */
    public function testUpdateTournamentAction()
    {
        foreach (self::$tournaments as $tournament) {
            if ($tournament['toEdit']) {
                $id = $tournament['id'];
                $request = new Request('PUT', '/tournaments/' . $id, $this->requestConfig, json_encode([
                    'title' => $tournament['new'],
                ]));
                $response = $this->client->send($request);
                $this->assertEquals(204, $response->getStatusCode());
            }
        }
    }

    /**
     * @depends testCreateTournamentAction
     */
    public function testDeleteTournamentAction()
    {
        foreach (self::$tournaments as $key => $tournament) {
            if ($tournament['toDelete']) {
                $request = new Request('DELETE', '/tournaments/'.$tournament['id'], $this->requestConfig);
                $response = $this->client->send($request);
                $this->assertEquals(204, $response->getStatusCode());
            }
        }
    }
}