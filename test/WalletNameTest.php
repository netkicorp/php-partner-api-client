<?php

/**
 * Created by PhpStorm.
 * User: frank
 * Date: 11/6/15
 * Time: 4:00 PM
 */
class WalletNameTest extends PHPUnit_Framework_TestCase
{
    public $processRequestMock;
    public $walletName;

    public function setUp()
    {
        $this->processRequestMock = $this->getMockBuilder('\Netki\Request')
            ->setMethods(array('process_request'))
            ->getMock();

        $this->walletName = new Netki\WalletName('domain_name', 'name', 'extId');
        $this->walletName->set_api_opts('apiKey', 'apiUrl', 'partnerId');
        $this->walletName->set_requestor($this->processRequestMock);
    }

    public function testInit()
    {
        $wn = new Netki\WalletName('domain_name', 'name', 'extId', 'id');

        $this->assertEquals('domain_name', $wn->domainName);
        $this->assertEquals('name', $wn->name);
        $this->assertEquals('extId', $wn->externalId);
        $this->assertEquals('id', $wn->id);
    }

    public function testGetUsedCurrencies()
    {
        // Setup test
        $this->walletName->set_currency_address('btc', '1btcaddy');

        // Execute test
        $response = $this->walletName->get_used_currencies();

        // Validate Result
        $this->assertEquals(array('btc'), $response);
    }

    public function testGetWalletAddress()
    {
        // Setup test
        $this->walletName->set_currency_address('btc', '1btcaddy');

        // Execute test
        $response = $this->walletName->get_wallet_address('btc');

        // Validate Result
        $this->assertEquals('1btcaddy', $response);
    }

    public function testSetCurrencyAddress()
    {
        // Execute test
        $this->walletName->set_currency_address('btc', '1btcaddy');

        // Validate Result
        $this->assertEquals('1btcaddy', $this->walletName->get_wallet_address('btc'));
        $this->assertEquals(array('btc'), $this->walletName->get_used_currencies());
    }

    public function testRemoveCurrencyAddress()
    {
        // Setup test
        $this->walletName->set_currency_address('btc', '1btcaddy');
        $this->walletName->set_currency_address('dgc', 'Doggyaddy');

        // Execute test
        $this->walletName->remove_currency_address('btc');

        // Validate Result
        $this->assertEquals(array('dgc'), $this->walletName->get_used_currencies());
    }

    public function testSaveNoIdCreateCase()
    {
        // Setup expected POST data to process_request call
        $walletData = array();
        $walletData['name'] = 'name';
        $walletData['domain_name'] = 'domain_name';
        $walletData['external_id'] = 'extId';
        $walletData['wallets'] = array();
        $walletData['wallets'][0] = array('currency'=>'btc', 'wallet_address'=>'1btcaddy');
        $walletData['wallets'][1] = array('currency'=>'dgc', 'wallet_address'=>'Doggyaddy');
        $fullRequest = array('wallet_names'=>array($walletData));

        // Setup process_request mock for test
        $this->processRequestMock->expects($this->once())
            ->method('process_request')
            ->with(
                $this->equalTo('partnerId'),
                $this->equalTo('apiKey'),
                $this->equalTo('apiUrl/v1/partner/walletname'),
                $this->equalTo('POST'),
                $this->equalTo($fullRequest)
            );

        // Setup object in test
        $this->walletName->set_currency_address('btc', '1btcaddy');
        $this->walletName->set_currency_address('dgc', 'Doggyaddy');

        // Execute test
        $this->walletName->save();

        // Validating POST data in process_request call. No return data.
    }

    public function testSaveIDProvidedUpdateCase()
    {
        // Setup expected PUT data to process_request call
        $walletData = array();
        $walletData['id'] = 'id';
        $walletData['name'] = 'name';
        $walletData['domain_name'] = 'domain_name';
        $walletData['external_id'] = 'extId';
        $walletData['wallets'] = array();
        $walletData['wallets'][0] = array('currency'=>'btc', 'wallet_address'=>'1btcaddy');
        $walletData['wallets'][1] = array('currency'=>'dgc', 'wallet_address'=>'Doggyaddy');
        $fullRequest = array('wallet_names'=>array($walletData));

        // Setup process_request mock for test
        $this->processRequestMock->expects($this->once())
            ->method('process_request')
            ->with(
                $this->equalTo('partnerId'),
                $this->equalTo('apiKey'),
                $this->equalTo('apiUrl/v1/partner/walletname'),
                $this->equalTo('PUT'),
                $this->equalTo($fullRequest)
            );

        // Setup object in test
        $this->walletName = new Netki\WalletName('domain_name', 'name', 'extId', 'id');
        $this->walletName->set_api_opts('apiKey', 'apiUrl', 'partnerId');
        $this->walletName->set_requestor($this->processRequestMock);
        $this->walletName->set_currency_address('btc', '1btcaddy');
        $this->walletName->set_currency_address('dgc', 'Doggyaddy');

        // Execute test
        $this->walletName->save();

        // Validating PUT data in process_request call. No return data.
    }

    public function testDelete()
    {
        // Setup expected DELETE data to process_request call
        $walletData = array();
        $walletData['domain_name'] = 'domain_name';
        $walletData['id'] = 'id';
        $fullRequest = array('wallet_names'=>array($walletData));

        // Setup process_request mock for test
        $this->processRequestMock->expects($this->once())
            ->method('process_request')
            ->with(
                $this->equalTo('partnerId'),
                $this->equalTo('apiKey'),
                $this->equalTo('apiUrl/v1/partner/walletname/domain_name/id'),
                $this->equalTo('DELETE'),
                $this->equalTo(null)
            );

        // Setup object in test
        $this->walletName = new Netki\WalletName('domain_name', 'name', 'extId', 'id');
        $this->walletName->set_api_opts('apiKey', 'apiUrl', 'partnerId');
        $this->walletName->set_requestor($this->processRequestMock);

        // Execute test
        $this->walletName->delete();

        // Validating DELETE data in process_request call. No return data.
    }

    public function testDeleteMissingId()
    {
        // Execute test
        try
        {
            $this->walletName->delete();
        }
        catch (Exception $e)
        {
            $this->assertEquals('Unable to Delete Object that Does Not Exist Remotely', $e->getMessage());
            return;
        }
        $this->fail('Expected exception to be raised.');
    }
}
