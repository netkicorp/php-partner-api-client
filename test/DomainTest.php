<?php

/**
 * Created by PhpStorm.
 * User: frank
 * Date: 11/5/15
 * Time: 7:26 PM
 */
class DomainTest extends PHPUnit_Framework_TestCase
{
    public $processRequestMock;

    public function setUp()
    {
        $this->processRequestMock = $this->getMockBuilder('\Netki\Request')
            ->setMethods(array('process_request'))
            ->getMock();
    }

    public function testInit()
    {
        $domain = new Netki\Domain('domain_name');

        $this->assertEquals('domain_name', $domain->name);
        $this->assertNull($domain->status);
        $this->assertNull($domain->delegationStatus);
        $this->assertNull($domain->delegationMessage);
        $this->assertNull($domain->walletNameCount);
        $this->assertNull($domain->nextRoll);
        $this->assertEquals(array(), $domain->dsRecords);
        $this->assertEquals(array(), $domain->nameservers);
        $this->assertNull($domain->publicSigningKey);
    }

    public function testDelete()
    {
        // Setup process_request mock for test
        $this->processRequestMock->expects($this->once())
            ->method('process_request')
            ->with(
                $this->equalTo('partnerId'),
                $this->equalTo('apiKey'),
                $this->equalTo('apiUrl/v1/partner/domain/domain_name'),
                $this->equalTo('DELETE'),
                $this->equalTo(null)
            );

        // Setup object in test
        $domain = new Netki\Domain('domain_name');
        $domain->set_api_opts('apiKey', 'apiUrl', 'partnerId');
        $domain->set_requestor($this->processRequestMock);

        // Execute test
        $domain->delete();

        // No additional validation as there is no return value from delete().
        // with() above validates call to process_request
    }

    public function testLoadStatus()
    {
        // Setup mock API response object
        $mockResponse = new stdClass();
        $mockResponse->status = 'status';
        $mockResponse->delegation_status = 'delegation_status';
        $mockResponse->delegation_message = 'delegation_message';
        $mockResponse->wallet_name_count = 10;

        // Setup process_request mock for test
        $this->processRequestMock->expects($this->once())
            ->method('process_request')
            ->with(
                $this->equalTo('partnerId'),
                $this->equalTo('apiKey'),
                $this->equalTo('apiUrl/v1/partner/domain/domain_name'),
                $this->equalTo('GET'),
                $this->equalTo(null)
            )
            ->willReturn($mockResponse);

        // Setup object in test
        $domain = new Netki\Domain('domain_name');
        $domain->set_api_opts('apiKey', 'apiUrl', 'partnerId');
        $domain->set_requestor($this->processRequestMock);

        // Execute test
        $domain->load_status();

        // Validate values are properly set in object in test
        $this->assertEquals($mockResponse->status, $domain->status);
        $this->assertEquals($mockResponse->delegation_status, $domain->delegationStatus);
        $this->assertEquals($mockResponse->delegation_message, $domain->delegationMessage);
        $this->assertEquals($mockResponse->wallet_name_count, $domain->walletNameCount);
    }

    public function testLoadDnssecDetailsGoRight()
    {
        // Setup mock API response object
        $mockResponse = new stdClass();
        $mockResponse->public_key_signing_key = 'pksk';
        $mockResponse->ds_records = array('record1', 'record2');
        $mockResponse->nameservers = array('ns1', 'ns2');
        $mockResponse->nextroll_date = '2016-01-11 14:30:10';

        // Setup process_request mock for test
        $this->processRequestMock->expects($this->once())
            ->method('process_request')
            ->with(
                $this->equalTo('partnerId'),
                $this->equalTo('apiKey'),
                $this->equalTo('apiUrl/v1/partner/domain/dnssec/domain_name'),
                $this->equalTo('GET'),
                $this->equalTo(null)
            )
            ->willReturn($mockResponse);

        // Setup object in test
        $domain = new Netki\Domain('domain_name');
        $domain->set_api_opts('apiKey', 'apiUrl', 'partnerId');
        $domain->set_requestor($this->processRequestMock);

        // Execute test
        $domain->load_dnssec_details();

        // Validate values are properly set in object in test
        $this->assertEquals($mockResponse->public_key_signing_key, $domain->publicSigningKey);
        $this->assertEquals($mockResponse->ds_records, $domain->dsRecords);
        $this->assertEquals($mockResponse->nameservers, $domain->nameservers);
        date_default_timezone_set('UTC');
        $this->assertEquals(\DateTime::createFromFormat('Y-m-d H:i:s', $mockResponse->nextroll_date), $domain->nextRoll);
    }

    public function testLoadDnssecDetailsMissingPKSK()
    {
        // Setup mock API response object
        $mockResponse = new stdClass();
        $mockResponse->ds_records = array('record1', 'record2');
        $mockResponse->nameservers = array('ns1', 'ns2');
        $mockResponse->nextroll_date = '2016-01-11 14:30:10';

        // Setup process_request mock for test
        $this->processRequestMock->expects($this->once())
            ->method('process_request')
            ->with(
                $this->equalTo('partnerId'),
                $this->equalTo('apiKey'),
                $this->equalTo('apiUrl/v1/partner/domain/dnssec/domain_name'),
                $this->equalTo('GET'),
                $this->equalTo(null)
            )
            ->willReturn($mockResponse);

        // Setup object in test
        $domain = new Netki\Domain('domain_name');
        $domain->set_api_opts('apiKey', 'apiUrl', 'partnerId');
        $domain->set_requestor($this->processRequestMock);

        // Execute test
        $domain->load_dnssec_details();

        // Validate values are properly set in object in test
        $this->assertNull($domain->publicSigningKey);
        $this->assertEquals($mockResponse->ds_records, $domain->dsRecords);
        $this->assertEquals($mockResponse->nameservers, $domain->nameservers);
        date_default_timezone_set('UTC');
        $this->assertEquals(\DateTime::createFromFormat('Y-m-d H:i:s', $mockResponse->nextroll_date), $domain->nextRoll);
    }

    public function testLoadDnssecDetailsMissingDSRecords()
    {
        // Setup mock API response object
        $mockResponse = new stdClass();
        $mockResponse->public_key_signing_key = 'pksk';
        $mockResponse->nameservers = array('ns1', 'ns2');
        $mockResponse->nextroll_date = '2016-01-11 14:30:10';

        // Setup process_request mock for test
        $this->processRequestMock->expects($this->once())
            ->method('process_request')
            ->with(
                $this->equalTo('partnerId'),
                $this->equalTo('apiKey'),
                $this->equalTo('apiUrl/v1/partner/domain/dnssec/domain_name'),
                $this->equalTo('GET'),
                $this->equalTo(null)
            )
            ->willReturn($mockResponse);

        // Setup object in test
        $domain = new Netki\Domain('domain_name');
        $domain->set_api_opts('apiKey', 'apiUrl', 'partnerId');
        $domain->set_requestor($this->processRequestMock);

        // Execute test
        $domain->load_dnssec_details();

        // Validate values are properly set in object in test
        $this->assertEquals($mockResponse->public_key_signing_key, $domain->publicSigningKey);
        $this->assertEquals(array(), $domain->dsRecords);
        $this->assertEquals($mockResponse->nameservers, $domain->nameservers);
        date_default_timezone_set('UTC');
        $this->assertEquals(\DateTime::createFromFormat('Y-m-d H:i:s', $mockResponse->nextroll_date), $domain->nextRoll);
    }

    public function testLoadDnssecDetailsMissingNameservers()
    {
        // Setup mock API response object
        $mockResponse = new stdClass();
        $mockResponse->public_key_signing_key = 'pksk';
        $mockResponse->ds_records = array('record1', 'record2');
        $mockResponse->nextroll_date = '2016-01-11 14:30:10';

        // Setup process_request mock for test
        $this->processRequestMock->expects($this->once())
            ->method('process_request')
            ->with(
                $this->equalTo('partnerId'),
                $this->equalTo('apiKey'),
                $this->equalTo('apiUrl/v1/partner/domain/dnssec/domain_name'),
                $this->equalTo('GET'),
                $this->equalTo(null)
            )
            ->willReturn($mockResponse);

        // Setup object in test
        $domain = new Netki\Domain('domain_name');
        $domain->set_api_opts('apiKey', 'apiUrl', 'partnerId');
        $domain->set_requestor($this->processRequestMock);

        // Execute test
        $domain->load_dnssec_details();

        // Validate values are properly set in object in test
        $this->assertEquals($mockResponse->public_key_signing_key, $domain->publicSigningKey);
        $this->assertEquals($mockResponse->ds_records, $domain->dsRecords);
        $this->assertEquals(array(), $domain->nameservers);
        date_default_timezone_set('UTC');
        $this->assertEquals(\DateTime::createFromFormat('Y-m-d H:i:s', $mockResponse->nextroll_date), $domain->nextRoll);
    }

    public function testLoadDnssecDetailsMissingNextRoll()
    {
        // Setup mock API response object
        $mockResponse = new stdClass();
        $mockResponse->public_key_signing_key = 'pksk';
        $mockResponse->ds_records = array('record1', 'record2');
        $mockResponse->nameservers = array('ns1', 'ns2');

        // Setup process_request mock for test
        $this->processRequestMock->expects($this->once())
            ->method('process_request')
            ->with(
                $this->equalTo('partnerId'),
                $this->equalTo('apiKey'),
                $this->equalTo('apiUrl/v1/partner/domain/dnssec/domain_name'),
                $this->equalTo('GET'),
                $this->equalTo(null)
            )
            ->willReturn($mockResponse);

        // Setup object in test
        $domain = new Netki\Domain('domain_name');
        $domain->set_api_opts('apiKey', 'apiUrl', 'partnerId');
        $domain->set_requestor($this->processRequestMock);

        // Execute test
        $domain->load_dnssec_details();

        // Validate values are properly set in object in test
        $this->assertEquals($mockResponse->public_key_signing_key, $domain->publicSigningKey);
        $this->assertEquals($mockResponse->ds_records, $domain->dsRecords);
        $this->assertEquals($mockResponse->nameservers, $domain->nameservers);
        date_default_timezone_set('UTC');
        $this->assertNull($domain->nextRoll);
    }
}
