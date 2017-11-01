<?php
/**
 * Created by PhpStorm.
 * User: wallace
 * Date: 07/08/17
 * Time: 15:14
 */

namespace Tests\AppBundle\Controller;


use GuzzleHttp\Psr7\Request;
use Tests\AppBundle\ApiTestCase;

class MatchEventControllerTest extends ApiTestCase
{
    const KICKOFF_PAST = '-';
    const KICKOFF_FUTURE = '+';

    public static $matchEvents = [
        'c1' => [
            [
                'id' => null,
                'home' => 'rma',
                'away' => 'psg',
                'title' => '1/4 de finale aller',
                'kickOffType' => self::KICKOFF_PAST,
                'kickOff' => null,
                'toEdit' => true,
                'toDelete' => false,
            ],
            [
                'id' => null,
                'home' => 'rma',
                'away' => 'psg',
                'title' => '1/4 de finale retour',
                'kickOffType' => self::KICKOFF_FUTURE,
                'kickOff' => null,
                'toEdit' => false,
                'toDelete' => false,
            ],
            [
                'id' => null,
                'home' => 'psg',
                'away' => 'rma',
                'title' => 'match amical',
                'kickOffType' => null,
                'kickOff' => '2017-05-06T20:45:00+02:00',
                'toEdit' => false,
                'toDelete' => true,
            ],
        ],
        'r1' => [
            [
                'id' => null,
                'home' => 'r92',
                'away' => 'nsa',
                'title' => 'match de poule aller',
                'kickOffType' => self::KICKOFF_PAST,
                'kickOff' => null,
                'toEdit' => false,
                'toDelete' => false,
            ],
            [
                'id' => null,
                'home' => 'r92',
                'away' => 'nsa',
                'title' => 'match de poule retour',
                'kickOffType' => self::KICKOFF_FUTURE,
                'kickOff' => null,
                'toEdit' => true,
                'toDelete' => false,
            ],
            [
                'id' => null,
                'home' => 'r92',
                'away' => 'nsa',
                'title' => 'test match',
                'kickOffType' => null,
                'kickOff' => null,
                'toEdit' => false,
                'toDelete' => true,
            ],
        ],
    ];

    public function testCreateMatchEventAction()
    {
        foreach (self::$matchEvents as $tournamentTitle => $tournamentEvents) {
            $tournament = TournamentControllerTest::$tournaments[$tournamentTitle];
            $tournamentId = $tournament['id'];
            foreach ($tournamentEvents as $index => $event) {
                $homeTeam = TeamControllerTest::$teams[$event['home']];
                $homeTeamId = $homeTeam['id'];
                $awayTeam = TeamControllerTest::$teams[$event['away']];
                $awayTeamId = $awayTeam['id'];

                switch ($event['kickOffType']) {
                    case self::KICKOFF_PAST:
                    case self::KICKOFF_FUTURE:
                        $kickOff = (new \DateTime())
                            ->modify($event['kickOffType'].rand(15,30).' days')
                            ->setTime(rand(0,7)+15,rand(0,3)*15)
                            ->format('c');
                        break;
                    default:
                        $kickOff = $event['kickOff'];
                }

                $request = new Request('POST', '/match-events', $this->requestConfig, json_encode([
                    'tournamentId' => $tournamentId,
                    'homeTeamId' => $homeTeamId,
                    'awayTeamId' => $awayTeamId,
                    'title' => $event['title'],
                    'kickOff' => $kickOff,
                ]));
                $response = $this->client->send($request);
                $this->assertEquals(201,$response->getStatusCode());
                $body = json_decode((string) $response->getBody());
                $this->assertObjectHasAttribute('id',$body);
                $this->assertObjectHasAttribute('kickOff',$body);
                if (!is_null($kickOff)) {
                    $kickOff = (new \DateTime($kickOff))->format('c');
                }
                $this->assertAttributeEquals($kickOff,'kickOff',$body);

                $this->assertObjectHasAttribute('title',$body);
                $this->assertAttributeEquals($event['title'],'title',$body);

                $this->assertObjectHasAttribute('tournament',$body);
                $this->assertObjectHasAttribute('id',$body->tournament);
                $this->assertAttributeEquals($tournamentId,'id',$body->tournament);
                $this->assertObjectHasAttribute('title',$body->tournament);
                $this->assertAttributeEquals($tournament['new'],'title',$body->tournament);

                $this->assertObjectHasAttribute('homeTeam',$body);
                $this->assertObjectHasAttribute('id',$body->homeTeam);
                $this->assertAttributeEquals($homeTeamId,'id',$body->homeTeam);
                $this->assertObjectHasAttribute('name',$body->homeTeam);
                $this->assertAttributeEquals($homeTeam['new'],'name',$body->homeTeam);

                $this->assertObjectHasAttribute('awayTeam',$body);
                $this->assertObjectHasAttribute('id',$body->awayTeam);
                $this->assertAttributeEquals($awayTeamId,'id',$body->awayTeam);
                $this->assertObjectHasAttribute('name',$body->awayTeam);
                $this->assertAttributeEquals($awayTeam['new'],'name',$body->awayTeam);

                self::$matchEvents[$tournamentTitle][$index]['id'] = $body->id;
                self::$matchEvents[$tournamentTitle][$index]['kickOff'] = $body->kickOff;
            }
        }
    }

    /**
     * @depends testCreateMatchEventAction
     */
    public function testListMatchEventsAction()
    {
        $cpt = 0;
        foreach (self::$matchEvents as $tournamentEvents) {
            $cpt += count($tournamentEvents);
        }
        $request = new Request('GET','/match-events',$this->requestConfig);
        $response = $this->client->send($request);
        $this->assertEquals(200,$response->getStatusCode());
        $body = json_decode((string) $response->getBody());
        $this->assertCount($cpt,$body);
    }

    /**
     * @depends testCreateMatchEventAction
     */
    public function testShowMatchEventsAction()
    {
        foreach (self::$matchEvents as $tournamentTitle => $tournamentEvents) {
            $tournament = TournamentControllerTest::$tournaments[$tournamentTitle];
            $tournamentId = $tournament['id'];
            foreach ($tournamentEvents as $index => $event) {
                $homeTeam = TeamControllerTest::$teams[$event['home']];
                $homeTeamId = $homeTeam['id'];
                $awayTeam = TeamControllerTest::$teams[$event['away']];
                $awayTeamId = $awayTeam['id'];
                $request = new Request('GET', '/match-events/'.$event['id'], $this->requestConfig);
                $response = $this->client->send($request);
                $this->assertEquals(200,$response->getStatusCode());
                $body = json_decode((string) $response->getBody());
                $this->assertObjectHasAttribute('id',$body);
                $this->assertObjectHasAttribute('kickOff',$body);
                $this->assertAttributeEquals($event['kickOff'],'kickOff',$body);

                $this->assertObjectHasAttribute('title',$body);
                $this->assertAttributeEquals($event['title'],'title',$body);

                $this->assertObjectHasAttribute('tournament',$body);
                $this->assertObjectHasAttribute('id',$body->tournament);
                $this->assertAttributeEquals($tournamentId,'id',$body->tournament);
                $this->assertObjectHasAttribute('title',$body->tournament);
                $this->assertAttributeEquals($tournament['new'],'title',$body->tournament);

                $this->assertObjectHasAttribute('homeTeam',$body);
                $this->assertObjectHasAttribute('id',$body->homeTeam);
                $this->assertAttributeEquals($homeTeamId,'id',$body->homeTeam);
                $this->assertObjectHasAttribute('name',$body->homeTeam);
                $this->assertAttributeEquals($homeTeam['new'],'name',$body->homeTeam);

                $this->assertObjectHasAttribute('awayTeam',$body);
                $this->assertObjectHasAttribute('id',$body->awayTeam);
                $this->assertAttributeEquals($awayTeamId,'id',$body->awayTeam);
                $this->assertObjectHasAttribute('name',$body->awayTeam);
                $this->assertAttributeEquals($awayTeam['new'],'name',$body->awayTeam);
            }
        }
    }

    /**
     * @depends testCreateMatchEventAction
     */
    public function testDeleteMatchEventAction()
    {
        foreach (self::$matchEvents as $tournamentEvents) {
            foreach ($tournamentEvents as $event) {
                if ($event['toDelete']) {
                    $request = new Request('DELETE', '/match-events/'.$event['id'], $this->requestConfig);
                    $response = $this->client->send($request);
                    $this->assertEquals(204, $response->getStatusCode());
                }
            }
        }
    }

    /**
     * @depends testCreateMatchEventAction
     */
    public function testUpdateMatchEventAction()
    {
        foreach (self::$matchEvents  as $tournamentTitle => $tournamentEvents) {
            $tournament = TournamentControllerTest::$tournaments[$tournamentTitle];
            $tournamentId = $tournament['id'];
            foreach ($tournamentEvents as $index => $event) {
                $homeTeam = TeamControllerTest::$teams[$event['home']];
                $homeTeamId = $homeTeam['id'];
                $awayTeam = TeamControllerTest::$teams[$event['away']];
                $awayTeamId = $awayTeam['id'];

                switch ($event['kickOffType']) {
                    case self::KICKOFF_PAST:
                    case self::KICKOFF_FUTURE:
                        $kickOff = (new \DateTime())
                            ->modify($event['kickOffType'].rand(15,30).' days')
                            ->setTime(rand(0,7)+15,rand(0,3)*15)
                            ->format('c');
                        break;
                    default:
                        $kickOff = $event['kickOff'];
                }

                if ($event['toEdit']) {
                    $request = new Request('PUT', '/match-events/'.$event['id'], $this->requestConfig, json_encode([
                        'tournamentId' => $tournamentId,
                        'homeTeamId' => $awayTeamId,
                        'awayTeamId' => $homeTeamId,
                        'title' => $event['title'],
                        'kickOff' => $kickOff,
                    ]));
                    $response = $this->client->send($request);
                    $this->assertEquals(204, $response->getStatusCode());
                }
            }
        }
    }
}