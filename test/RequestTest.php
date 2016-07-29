<?php

/**
 * Created by PhpStorm.
 * User: frank
 * Date: 11/3/15
 * Time: 7:12 PM
 */

class RequestTest extends \PHPUnit_Framework_TestCase
{
    use \InterNations\Component\HttpMock\PHPUnit\HttpMockTrait;

    const PARTNERID = 'testPartnerId';
    const APIKEY = 'apiKey';
    const APIURL = 'http://localhost:8082/v1/partner/walletname';

    public static function setUpBeforeClass()
    {
        static::setUpHttpMockBeforeClass('8082', 'localhost');
    }

    public static function tearDownAfterClass()
    {
        static::tearDownHttpMockAfterClass();
    }

    public function setUp()
    {
        $this->setUpHttpMock();
    }

    public function tearDown()
    {
        $this->tearDownHttpMock();
    }

    public function testRequestGoRight200()
    {
        // TODO: Figure out a way to validate post data?

        // Setup HTTP Mock
        $this->http->mock
            ->when()
            ->methodIs('GET')
            ->pathIs('/v1/partner/walletname')
            ->then()
            ->statusCode(200)
            ->header('content-type', 'application/json')
            ->body(json_encode(array('success'=>true)))
            ->end();
        $this->http->setUp();

        // Call Function in Test
        $client = new Netki\Request();

        $response = $client->process_request(
            RequestTest::PARTNERID,
            RequestTest::APIKEY,
            RequestTest::APIURL,
            'GET',
            null
        );

        // Validate Response
        $this->assertTrue($response->success);
        $this->assertEquals(RequestTest::APIKEY, $this->http->requests->latest()->getHeader('Authorization'));
        $this->assertEquals(RequestTest::PARTNERID, $this->http->requests->latest()->getHeader('X-Partner-ID'));
        $this->assertEquals('application/json', $this->http->requests->latest()->getHeader('content-type'));
    }

    public function testRequestGoRight201()
    {
        // Setup HTTP Mock
        $this->http->mock
            ->when()
            ->methodIs('PUT')
            ->pathIs('/v1/partner/walletname')
            ->then()
            ->statusCode(201)
            ->header('content-type', 'application/json')
            ->body(json_encode(array('success'=>true)))
            ->end();
        $this->http->setUp();

        // Call Function in Test
        $client = new Netki\Request();
        $response = $client->process_request(
            RequestTest::PARTNERID,
            RequestTest::APIKEY,
            RequestTest::APIURL,
            'PUT',
            null
        );

        // Validate Response
        $this->assertTrue($response->success);
        $this->assertEquals(RequestTest::APIKEY, $this->http->requests->latest()->getHeader('Authorization'));
        $this->assertEquals(RequestTest::PARTNERID, $this->http->requests->latest()->getHeader('X-Partner-ID'));
        $this->assertEquals('application/json', $this->http->requests->latest()->getHeader('content-type'));
    }

    public function testRequestGoRight202()
    {
        // Setup HTTP Mock
        $this->http->mock
            ->when()
            ->methodIs('POST')
            ->pathIs('/v1/partner/walletname')
            ->then()
            ->statusCode(202)
            ->header('content-type', 'application/json')
            ->body(json_encode(array('success'=>true)))
            ->end();
        $this->http->setUp();

        // Call Function in Test
        $client = new Netki\Request();
        $response = $client->process_request(
            RequestTest::PARTNERID,
            RequestTest::APIKEY,
            RequestTest::APIURL,
            'POST',
            array(json_encode(array('key' => 'value')))
        );

        // Validate Response
        $this->assertTrue($response->success);
        $this->assertEquals(RequestTest::APIKEY, $this->http->requests->latest()->getHeader('Authorization'));
        $this->assertEquals(RequestTest::PARTNERID, $this->http->requests->latest()->getHeader('X-Partner-ID'));
        $this->assertEquals('application/json', $this->http->requests->latest()->getHeader('content-type'));
        $this->assertEquals(json_encode(array('key' => 'value')), json_decode($this->http->requests->latest()->getBody()->read(1024))[0]);

//        var_dump(array(json_encode(array('key' => 'value'))));
//        echo $this->http->requests->latest()->getBody();
    }

    public function testRequestGoRight204()
    {
        // Setup HTTP Mock
        $this->http->mock
            ->when()
            ->methodIs('DELETE')
            ->pathIs('/v1/partner/walletname')
            ->then()
            ->statusCode(204)
            ->header('content-type', 'application/json')
            ->body(json_encode(array('success'=>true)))
            ->end();
        $this->http->setUp();

        // Call Function in Test
        $client = new Netki\Request();
        $response = $client->process_request(
            RequestTest::PARTNERID,
            RequestTest::APIKEY,
            RequestTest::APIURL,
            'DELETE',
            null
        );

        // Validate Response
        $this->assertEquals(array(), $response);
    }

    public function testRequestUnsupportedMethod()
    {
        // Call Function in Test
        $client = new Netki\Request();
        try
        {
            $client->process_request(
                RequestTest::PARTNERID,
                RequestTest::APIKEY,
                RequestTest::APIURL,
                'PATCH',
                null
            );
        }
        catch (Exception $e)
        {
            $this->assertEquals('Unsupported method: PATCH', $e->getMessage());
            return;
        }
        $this->fail('Expected exception to be raised.');
    }

    public function testRequestErrorParsingJSON()
    {
        // Setup HTTP Mock
        $this->http->mock
            ->when()
            ->methodIs('GET')
            ->pathIs('/v1/partner/walletname')
            ->then()
            ->statusCode(200)
            ->header('content-type', 'application/json')
            ->body(null)
            ->end();
        $this->http->setUp();

        // Call Function in Test
        $client = new Netki\Request();
        try
        {
            $client->process_request(
                RequestTest::PARTNERID,
                RequestTest::APIKEY,
                RequestTest::APIURL,
                'GET',
                null
            );
        }
        catch (Exception $e)
        {
            $this->assertEquals('Error parsing JSON Data', $e->getMessage());
            return;
        }
        $this->fail('Expected exception to be raised.');
    }

    public function testRequestSuccessFalseNoFailuresArray()
    {
        // Setup HTTP Mock
        $this->http->mock
            ->when()
            ->methodIs('GET')
            ->pathIs('/v1/partner/walletname')
            ->then()
            ->statusCode(200)
            ->header('content-type', 'application/json')
            ->body(json_encode(array('success' => false, 'message' => 'An error occurred.')))
            ->end();
        $this->http->setUp();

        // Call Function in Test
        $client = new Netki\Request();
        try
        {
            $client->process_request(
                RequestTest::PARTNERID,
                RequestTest::APIKEY,
                RequestTest::APIURL,
                'GET',
                null
            );
        }
        catch (Exception $e)
        {
            $this->assertEquals('Request Failed: An error occurred.', $e->getMessage());
            return;
        }
        $this->fail('Expected exception to be raised.');
    }

    public function testRequestStatusCode404NoFailuresArray()
    {
        // Setup HTTP Mock
        $this->http->mock
            ->when()
            ->methodIs('GET')
            ->pathIs('/v1/partner/walletname')
            ->then()
            ->statusCode(404)
            ->header('content-type', 'application/json')
            ->body(json_encode(array('success' => true, 'message' => 'Record not found.')))
            ->end();
        $this->http->setUp();

        // Call Function in Test
        $client = new Netki\Request();
        try
        {
            $client->process_request(
                RequestTest::PARTNERID,
                RequestTest::APIKEY,
                RequestTest::APIURL,
                'GET',
                null
            );
        }
        catch (Exception $e)
        {
            $this->assertEquals('Request Failed: Record not found.', $e->getMessage());
            return;
        }
        $this->fail('Expected exception to be raised.');
    }

    public function testRequestSuccessFalseWithFailuresArray()
    {
        // Define failure response body with failures array
        $failureResponse = array(
            'success' => false,
            'message' => 'An error occurred.',
            'failures' => array(array('message' => 'failure1'), array('message' => 'failure2'))
        );

        // Setup HTTP Mock
        $this->http->mock
            ->when()
            ->methodIs('GET')
            ->pathIs('/v1/partner/walletname')
            ->then()
            ->statusCode(200)
            ->header('content-type', 'application/json')
            ->body(json_encode($failureResponse))
            ->end();
        $this->http->setUp();

        // Call Function in Test
        $client = new Netki\Request();
        try
        {
            $client->process_request(
                RequestTest::PARTNERID,
                RequestTest::APIKEY,
                RequestTest::APIURL,
                'GET',
                null
            );
        }
        catch (Exception $e)
        {
            $this->assertEquals('Request Failed: An error occurred. [FAILURES: failure1, failure2]', $e->getMessage());
            return;
        }
        $this->fail('Expected exception to be raised.');
    }
}
