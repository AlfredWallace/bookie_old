<?php
/**
 * Created by PhpStorm.
 * User: wallace
 * Date: 26/07/17
 * Time: 14:34
 */

namespace Tests\AppBundle;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;

class ApiTestCase extends TestCase
{
    /** @var  Client */
    private static $staticClient;

    /** @var  Client */
    protected $client;

    protected $requestConfig;

    public static function setUpBeforeClass()
    {
        self::$staticClient = new Client([
            'base_uri' => 'http://127.0.0.1:8000',
            'defaults' => [
                'exceptions' => false,
            ],
        ]);
    }

    public function setUp()
    {
        $this->client = self::$staticClient;
        $request = new Request(
            'POST',
            '/login_check',
            ['Content-Type' => 'application/json'],
            json_encode([
                '_username' => 'wallace',
                '_password' => 'wallpass',
            ])
        );
        $response = $this->client->send($request);
        $body = json_decode((string) $response->getBody());
        if (property_exists($body,'token')) {
            $this->requestConfig = [
                'Authorization' => 'Bearer '.$body->token,
                'Content-Type' => 'application/json',
            ];
        }
    }

}