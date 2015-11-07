<?php

/**
 * Created by PhpStorm.
 * User: frank
 * Date: 11/6/15
 * Time: 12:29 PM
 */
class NetkiClientTest extends PHPUnit_Framework_TestCase
{
    public $processRequestMock;
    public $mockResponse;

    public function setUp()
    {
        // Setup Netki API mock
        $this->processRequestMock = $this->getMockBuilder('\Netki\Request')
            ->setMethods(array('process_request'))
            ->getMock();

        // Setup mock API response object
        $this->mockResponse = new stdClass();
        $this->mockResponse->wallet_name_count = 1;
        $this->mockResponse->wallet_names = array();

        $mockWalletName = new stdClass();
        $mockWalletName->id = 'id';
        $mockWalletName->domain_name = 'domain_name';
        $mockWalletName->name = 'name';
        $mockWalletName->external_id = 'extId';
        $mockWalletName->wallets = array();

        $mockWallet = new stdClass();
        $mockWallet->currency = 'btc';
        $mockWallet->wallet_address = '1btcaddy';
        $mockWalletName->wallets[] = $mockWallet;

        $mockWallet = new stdClass();
        $mockWallet->currency = 'dgc';
        $mockWallet->wallet_address = 'Doggyaddy';
        $mockWalletName->wallets[] = $mockWallet;

        $this->mockResponse->wallet_names[] = $mockWalletName;
    }

    /*
    * Wallet Name Operations
    */
    public function testGetWalletNamesGoRightNoDomainNoExternalIdOneWalletName()
    {
        // Setup process_request mock for test
        $this->processRequestMock->expects($this->once())
            ->method('process_request')
            ->with(
                $this->equalTo('partnerId'),
                $this->equalTo('apiKey'),
                $this->equalTo('apiUrl/v1/partner/walletname'),
                $this->equalTo('GET'),
                $this->equalTo(null)
            )
            ->willReturn($this->mockResponse);

        // Setup object in test
        $client = new Netki\NetkiClient('partnerId', 'apiKey', 'apiUrl');
        $client->set_requestor($this->processRequestMock);

        // Execute test
        $response = $client->get_wallet_names();

        // Validate values are properly set in object in test
        $testData = $this->mockResponse->wallet_names[0];
        $walletName = $response[0];
        $this->assertEquals($testData->id, $walletName->id);
        $this->assertEquals($testData->domain_name, $walletName->domainName);
        $this->assertEquals($testData->name, $walletName->name);
        $this->assertEquals($testData->external_id, $walletName->externalId);

        $mockWallets = array(
            'btc'=>$walletName->get_wallet_address('btc'),
            'dgc'=>$walletName->get_wallet_address('dgc')
        );
        $this->assertEquals(array('btc'=>'1btcaddy', 'dgc'=>'Doggyaddy'), $mockWallets);
    }

    public function testGetWalletNamesGoRightProvidedDomainNoExternalIdOneWalletName()
    {
        // Setup process_request mock for test
        $this->processRequestMock->expects($this->once())
            ->method('process_request')
            ->with(
                $this->equalTo('partnerId'),
                $this->equalTo('apiKey'),
                $this->equalTo('apiUrl/v1/partner/walletname?domain_name=my_domain'),
                $this->equalTo('GET'),
                $this->equalTo(null)
            )
            ->willReturn($this->mockResponse);

        // Setup object in test
        $client = new Netki\NetkiClient('partnerId', 'apiKey', 'apiUrl');
        $client->set_requestor($this->processRequestMock);

        // Execute test
        $response = $client->get_wallet_names('my_domain');

        // Validate values are properly set in object in test
        $testData = $this->mockResponse->wallet_names[0];
        $walletName = $response[0];
        $this->assertEquals($testData->id, $walletName->id);
        $this->assertEquals($testData->domain_name, $walletName->domainName);
        $this->assertEquals($testData->name, $walletName->name);
        $this->assertEquals($testData->external_id, $walletName->externalId);

        $mockWallets = array(
            'btc'=>$walletName->get_wallet_address('btc'),
            'dgc'=>$walletName->get_wallet_address('dgc')
        );
        $this->assertEquals(array('btc'=>'1btcaddy', 'dgc'=>'Doggyaddy'), $mockWallets);
    }

    public function testGetWalletNamesGoRightProvidedDomainProvidedExternalIdOneWalletName()
    {
        // Setup process_request mock for test
        $this->processRequestMock->expects($this->once())
            ->method('process_request')
            ->with(
                $this->equalTo('partnerId'),
                $this->equalTo('apiKey'),
                $this->equalTo('apiUrl/v1/partner/walletname?domain_name=my_domain&external_id=extId'),
                $this->equalTo('GET'),
                $this->equalTo(null)
            )
            ->willReturn($this->mockResponse);

        // Setup object in test
        $client = new Netki\NetkiClient('partnerId', 'apiKey', 'apiUrl');
        $client->set_requestor($this->processRequestMock);

        // Execute test
        $response = $client->get_wallet_names('my_domain', 'extId');

        // Validate values are properly set in object in test
        $testData = $this->mockResponse->wallet_names[0];
        $walletName = $response[0];
        $this->assertEquals($testData->id, $walletName->id);
        $this->assertEquals($testData->domain_name, $walletName->domainName);
        $this->assertEquals($testData->name, $walletName->name);
        $this->assertEquals($testData->external_id, $walletName->externalId);

        $mockWallets = array(
            'btc'=>$walletName->get_wallet_address('btc'),
            'dgc'=>$walletName->get_wallet_address('dgc')
        );
        $this->assertEquals(array('btc'=>'1btcaddy', 'dgc'=>'Doggyaddy'), $mockWallets);
    }

    public function testGetWalletNamesGoRightNoDomainNoExternalIdTwoWalletNames()
    {
        // Add additional Wallet Name object to test
        $mockWalletName = new stdClass();
        $mockWalletName->id = 'id';
        $mockWalletName->domain_name = 'domain_name';
        $mockWalletName->name = 'name';
        $mockWalletName->external_id = 'extId';
        $mockWalletName->wallets = array();

        $mockWallet = new stdClass();
        $mockWallet->currency = 'btc';
        $mockWallet->wallet_address = '1btcaddy';
        $mockWalletName->wallets[] = $mockWallet;

        $mockWallet = new stdClass();
        $mockWallet->currency = 'dgc';
        $mockWallet->wallet_address = 'Doggyaddy';
        $mockWalletName->wallets[] = $mockWallet;

        $this->mockResponse->wallet_names[] = $mockWalletName;


        // Setup process_request mock for test
        $this->processRequestMock->expects($this->once())
            ->method('process_request')
            ->with(
                $this->equalTo('partnerId'),
                $this->equalTo('apiKey'),
                $this->equalTo('apiUrl/v1/partner/walletname'),
                $this->equalTo('GET'),
                $this->equalTo(null)
            )
            ->willReturn($this->mockResponse);

        // Setup object in test
        $client = new Netki\NetkiClient('partnerId', 'apiKey', 'apiUrl');
        $client->set_requestor($this->processRequestMock);

        // Execute test
        $response = $client->get_wallet_names();

        // Validate Return Count
        $this->assertEquals(2, count($response));

        // Test Wallet Name One
        $walletName = $response[0];
        $testData = $this->mockResponse->wallet_names[0];
        $this->assertEquals($testData->id, $walletName->id);
        $this->assertEquals($testData->domain_name, $walletName->domainName);
        $this->assertEquals($testData->name, $walletName->name);
        $this->assertEquals($testData->external_id, $walletName->externalId);

        $mockWallets = array(
            'btc'=>$walletName->get_wallet_address('btc'),
            'dgc'=>$walletName->get_wallet_address('dgc')
        );
        $this->assertEquals(array('btc'=>'1btcaddy', 'dgc'=>'Doggyaddy'), $mockWallets);

        // Test Wallet Name 2
        $walletName = $response[1];
        $this->assertEquals(2, count($response));
        $this->assertEquals($testData->id, $walletName->id);
        $this->assertEquals($testData->domain_name, $walletName->domainName);
        $this->assertEquals($testData->name, $walletName->name);
        $this->assertEquals($testData->external_id, $walletName->externalId);

        $mockWallets = array(
            'btc'=>$walletName->get_wallet_address('btc'),
            'dgc'=>$walletName->get_wallet_address('dgc')
        );
        $this->assertEquals(array('btc'=>'1btcaddy', 'dgc'=>'Doggyaddy'), $mockWallets);
    }

    public function testCreateWalletName()
    {
        // Setup object in test
        $client = new Netki\NetkiClient('partnerId', 'apiKey', 'apiUrl');
        $response = $client->create_wallet_name('domain_name', 'name', 'extId');

        $this->assertEquals('domain_name', $response->domainName);
        $this->assertEquals('name', $response->name);
        $this->assertEquals('extId', $response->externalId);
        $this->assertNull($response->id);
        $this->assertEquals('apiKey', $response->get_apiKey());
        $this->assertEquals('apiUrl', $response->get_apiUrl());
        $this->assertEquals('partnerId', $response->get_partnerId());
    }

    /*
    * Partner Operations
    */
    public function testCreatePartner()
    {
        // Setup Mock API Response
        $mockPartnerResponse = new stdClass();
        $mockPartnerResponse->partner = new stdClass();
        $mockPartnerResponse->partner->id = 'partner_id';
        $mockPartnerResponse->partner->name = 'name';

        // Setup process_request mock for test
        $this->processRequestMock->expects($this->once())
            ->method('process_request')
            ->with(
                $this->equalTo('partnerId'),
                $this->equalTo('apiKey'),
                $this->equalTo('apiUrl/v1/admin/partner/partner_name'),
                $this->equalTo('POST'),
                $this->equalTo(null)
            )
            ->willReturn($mockPartnerResponse);

        // Setup object in test
        $client = new Netki\NetkiClient('partnerId', 'apiKey', 'apiUrl');
        $client->set_requestor($this->processRequestMock);

        // Execute test
        $response = $client->create_partner('partner_name');

        // Validate values are properly set in object in test
        $this->assertEquals($mockPartnerResponse->partner->id, $response->id);
        $this->assertEquals($mockPartnerResponse->partner->name, $response->name);
        $this->assertEquals('partnerId', $response->get_partnerId());
        $this->assertEquals('apiKey', $response->get_apiKey());
        $this->assertEquals('apiUrl', $response->get_apiUrl());
    }

    public function testGetPartnersOnePartner()
    {
        // Setup Mock API Response
        $mockPartnerResponse = new stdClass();
        $mockPartnerResponse->partners = array();

        $mockPartner = new stdClass();
        $mockPartner->id = 'partner_id';
        $mockPartner->name = 'name';
        $mockPartnerResponse->partners[] = $mockPartner;

        // Setup process_request mock for test
        $this->processRequestMock->expects($this->once())
            ->method('process_request')
            ->with(
                $this->equalTo('partnerId'),
                $this->equalTo('apiKey'),
                $this->equalTo('apiUrl/v1/admin/partner'),
                $this->equalTo('GET'),
                $this->equalTo(null)
            )
            ->willReturn($mockPartnerResponse);

        // Setup object in test
        $client = new Netki\NetkiClient('partnerId', 'apiKey', 'apiUrl');
        $client->set_requestor($this->processRequestMock);

        // Execute test
        $response = $client->get_partners();

        // Validate values are properly set in object in test

        $this->assertEquals($mockPartner->id, $response[0]->id);
        $this->assertEquals($mockPartner->name, $response[0]->name);
        $this->assertEquals('partnerId', $response[0]->get_partnerId());
        $this->assertEquals('apiKey', $response[0]->get_apiKey());
        $this->assertEquals('apiUrl', $response[0]->get_apiUrl());
    }

    public function testGetPartnersTwoPartners()
    {
        // Setup Mock API Response
        $mockPartnerResponse = new stdClass();
        $mockPartnerResponse->partners = array();

        $mockPartner = new stdClass();
        $mockPartner->id = 'partner_id';
        $mockPartner->name = 'name';
        $mockPartnerResponse->partners[] = $mockPartner;
        $mockPartnerResponse->partners[] = $mockPartner;

        // Setup process_request mock for test
        $this->processRequestMock->expects($this->once())
            ->method('process_request')
            ->with(
                $this->equalTo('partnerId'),
                $this->equalTo('apiKey'),
                $this->equalTo('apiUrl/v1/admin/partner'),
                $this->equalTo('GET'),
                $this->equalTo(null)
            )
            ->willReturn($mockPartnerResponse);

        // Setup object in test
        $client = new Netki\NetkiClient('partnerId', 'apiKey', 'apiUrl');
        $client->set_requestor($this->processRequestMock);

        // Execute test
        $response = $client->get_partners();
        $this->assertEquals(2, count($response));

        // Validate values are properly set in object in test
        // Partner One
        $this->assertEquals($mockPartner->id, $response[0]->id);
        $this->assertEquals($mockPartner->name, $response[0]->name);
        $this->assertEquals('partnerId', $response[0]->get_partnerId());
        $this->assertEquals('apiKey', $response[0]->get_apiKey());
        $this->assertEquals('apiUrl', $response[0]->get_apiUrl());
        // Partner Two
        $this->assertEquals($mockPartner->id, $response[1]->id);
        $this->assertEquals($mockPartner->name, $response[1]->name);
        $this->assertEquals('partnerId', $response[1]->get_partnerId());
        $this->assertEquals('apiKey', $response[1]->get_apiKey());
        $this->assertEquals('apiUrl', $response[1]->get_apiUrl());
    }

    public function testGetPartnersNoPartnersReturned()
    {
        // Setup Mock API Response
        $mockPartnerResponse = new stdClass();

        // Setup process_request mock for test
        $this->processRequestMock->expects($this->once())
            ->method('process_request')
            ->with(
                $this->equalTo('partnerId'),
                $this->equalTo('apiKey'),
                $this->equalTo('apiUrl/v1/admin/partner'),
                $this->equalTo('GET'),
                $this->equalTo(null)
            )
            ->willReturn($mockPartnerResponse);

        // Setup object in test
        $client = new Netki\NetkiClient('partnerId', 'apiKey', 'apiUrl');
        $client->set_requestor($this->processRequestMock);

        // Execute test
        $response = $client->get_partners();
        $this->assertEquals(array(), $response);
    }

    /*
    * Domain Operations
    */
    public function testCreateDomainWithoutPartner()
    {
        // Setup Mock API Response
        $mockCreateDomainResponse = new stdClass();
        $mockCreateDomainResponse->domain_name = 'domain_name';
        $mockCreateDomainResponse->status = 'status';
        $mockCreateDomainResponse->nameservers = array('ns1', 'ns2', 'ns3');

        // Setup process_request mock for test
        $this->processRequestMock->expects($this->once())
            ->method('process_request')
            ->with(
                $this->equalTo('partnerId'),
                $this->equalTo('apiKey'),
                $this->equalTo('apiUrl/v1/partner/domain/domain_name'),
                $this->equalTo('POST'),
                $this->equalTo(null)
            )
            ->willReturn($mockCreateDomainResponse);

        // Setup object in test
        $client = new Netki\NetkiClient('partnerId', 'apiKey', 'apiUrl');
        $client->set_requestor($this->processRequestMock);

        // Execute test
        $response = $client->create_domain('domain_name');

        // Validate values are properly set in object in test
        $this->assertEquals($mockCreateDomainResponse->domain_name, $response->name);
        $this->assertEquals($mockCreateDomainResponse->status, $response->status);
        $this->assertEquals($mockCreateDomainResponse->nameservers, $response->nameservers);
        $this->assertNull($response->delegationStatus);
        $this->assertNull($response->delegationMessage);
        $this->assertNull($response->walletNameCount);
        $this->assertNull($response->nextRoll);
        $this->assertEquals(array(), $response->dsRecords);
        $this->assertNull($response->publicSigningKey);
        $this->assertEquals('partnerId', $response->get_partnerId());
        $this->assertEquals('apiKey', $response->get_apiKey());
        $this->assertEquals('apiUrl', $response->get_apiUrl());
    }

    public function testCreateDomainWithPartner()
    {
        // Setup Mock API Response
        $mockCreateDomainResponse = new stdClass();
        $mockCreateDomainResponse->domain_name = 'domain_name';
        $mockCreateDomainResponse->status = 'status';
        $mockCreateDomainResponse->nameservers = array('ns1', 'ns2', 'ns3');

        // Setup process_request mock for test
        $this->processRequestMock->expects($this->once())
            ->method('process_request')
            ->with(
                $this->equalTo('partnerId'),
                $this->equalTo('apiKey'),
                $this->equalTo('apiUrl/v1/partner/domain/domain_name'),
                $this->equalTo('POST'),
                $this->equalTo(array('partner_id'=>'partner_id'))
            )
            ->willReturn($mockCreateDomainResponse);

        // Setup object in test
        $client = new Netki\NetkiClient('partnerId', 'apiKey', 'apiUrl');
        $client->set_requestor($this->processRequestMock);

        // Execute test
        $response = $client->create_domain('domain_name', 'partner_id');

        // Validate values are properly set in object in test
        $this->assertEquals($mockCreateDomainResponse->domain_name, $response->name);
        $this->assertEquals($mockCreateDomainResponse->status, $response->status);
        $this->assertEquals($mockCreateDomainResponse->nameservers, $response->nameservers);
        $this->assertNull($response->delegationStatus);
        $this->assertNull($response->delegationMessage);
        $this->assertNull($response->walletNameCount);
        $this->assertNull($response->nextRoll);
        $this->assertEquals(array(), $response->dsRecords);
        $this->assertNull($response->publicSigningKey);
        $this->assertEquals('partnerId', $response->get_partnerId());
        $this->assertEquals('apiKey', $response->get_apiKey());
        $this->assertEquals('apiUrl', $response->get_apiUrl());
    }

    public function testGetDomainsOneDomain()
    {
        // Setup Mock API Response
        $mockCreateDomainResponse = new stdClass();
        $mockCreateDomainResponse->domains = array();
        $mockDomain = new stdClass();
        $mockDomain->domain_name = 'domain_name';
        $mockCreateDomainResponse->domains[] = $mockDomain;

        $mockLoadStatusResponse = new stdClass();
        $mockLoadStatusResponse->status = 'status';
        $mockLoadStatusResponse->delegation_status = 'delegation_status';
        $mockLoadStatusResponse->delegation_message = 'delegation_message';
        $mockLoadStatusResponse->wallet_name_count = 10;

        $mockLoadDnssecResponse = new stdClass();
        $mockLoadDnssecResponse->public_key_signing_key = 'pksk';
        $mockLoadDnssecResponse->ds_records = array('record1', 'record2');
        $mockLoadDnssecResponse->nameservers = array('ns1', 'ns2');
        $mockLoadDnssecResponse->nextroll_date = '2016-01-11 14:30:10';

        $map = array(
            array('partnerId', 'apiKey', 'apiUrl/api/domain', 'GET', null, $mockCreateDomainResponse),
            array('partnerId', 'apiKey', 'apiUrl/v1/partner/domain/domain_name', 'GET', null, $mockLoadStatusResponse),
            array('partnerId', 'apiKey', 'apiUrl/v1/partner/domain/dnssec/domain_name', 'GET', null, $mockLoadDnssecResponse)
        );

        // Setup process_request mock for test
        $this->processRequestMock->expects($this->exactly(3))
            ->method('process_request')
            ->will($this->returnValueMap($map));

        // Setup object in test
        $client = new Netki\NetkiClient('partnerId', 'apiKey', 'apiUrl');
        $client->set_requestor($this->processRequestMock);

        // Execute test
        $response = $client->get_domains('domain_name');

        // Validate values are properly set in object in test
        $this->assertEquals($mockDomain->domain_name, $response[0]->name);
    }

    public function testGetDomainsTwoDomains()
    {
        // Setup Mock API Response
        $mockCreateDomainResponse = new stdClass();
        $mockCreateDomainResponse->domains = array();
        $mockDomain = new stdClass();
        $mockDomain->domain_name = 'domain_name';
        $mockCreateDomainResponse->domains[] = $mockDomain;
        $mockCreateDomainResponse->domains[] = $mockDomain;

        $mockLoadStatusResponse = new stdClass();
        $mockLoadStatusResponse->status = 'status';
        $mockLoadStatusResponse->delegation_status = 'delegation_status';
        $mockLoadStatusResponse->delegation_message = 'delegation_message';
        $mockLoadStatusResponse->wallet_name_count = 10;

        $mockLoadDnssecResponse = new stdClass();
        $mockLoadDnssecResponse->public_key_signing_key = 'pksk';
        $mockLoadDnssecResponse->ds_records = array('record1', 'record2');
        $mockLoadDnssecResponse->nameservers = array('ns1', 'ns2');
        $mockLoadDnssecResponse->nextroll_date = '2016-01-11 14:30:10';

        $map = array(
            array('partnerId', 'apiKey', 'apiUrl/api/domain', 'GET', null, $mockCreateDomainResponse),
            array('partnerId', 'apiKey', 'apiUrl/v1/partner/domain/domain_name', 'GET', null, $mockLoadStatusResponse),
            array('partnerId', 'apiKey', 'apiUrl/v1/partner/domain/dnssec/domain_name', 'GET', null, $mockLoadDnssecResponse),
            array('partnerId', 'apiKey', 'apiUrl/v1/partner/domain/domain_name', 'GET', null, $mockLoadStatusResponse),
            array('partnerId', 'apiKey', 'apiUrl/v1/partner/domain/dnssec/domain_name', 'GET', null, $mockLoadDnssecResponse)
        );

        // Setup process_request mock for test
        $this->processRequestMock->expects($this->exactly(5))
            ->method('process_request')
            ->will($this->returnValueMap($map));

        // Setup object in test
        $client = new Netki\NetkiClient('partnerId', 'apiKey', 'apiUrl');
        $client->set_requestor($this->processRequestMock);

        // Execute test
        $response = $client->get_domains('domain_name');
        $this->assertEquals(2, count($response));

        // Validate values are properly set in object in test
        $this->assertEquals($mockDomain->domain_name, $response[0]->name);
        $this->assertEquals($mockDomain->domain_name, $response[1]->name);
    }

    public function testGetDomainsNoDomains()
    {
        // Setup Mock API Response
        $mockCreateDomainResponse = new stdClass();
        $mockCreateDomainResponse->domains = array();

        $map = array(
            array('partnerId', 'apiKey', 'apiUrl/api/domain', 'GET', null, $mockCreateDomainResponse)
        );

        // Setup process_request mock for test
        $this->processRequestMock->expects($this->once())
            ->method('process_request')
            ->will($this->returnValueMap($map));

        // Setup object in test
        $client = new Netki\NetkiClient('partnerId', 'apiKey', 'apiUrl');
        $client->set_requestor($this->processRequestMock);

        // Execute test
        $response = $client->get_domains('domain_name');
        $this->assertEquals(array(), $response);
    }
}
