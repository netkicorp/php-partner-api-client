<?php

/**
 * Created by PhpStorm.
 * User: frank
 * Date: 11/5/15
 * Time: 6:42 PM
 */

class PartnerTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->processRequestMock = $this->getMockBuilder('\Netki\Request')
            ->setMethods(array('process_request'))
            ->getMock();
    }

    public function testInit()
    {
        $partner = new Netki\Partner('id', 'name');

        $this->assertEquals('id', $partner->id);
        $this->assertEquals('name', $partner->name);
    }

    public function testDelete()
    {
        // Setup process_request mock for test
        $this->processRequestMock->expects($this->once())
            ->method('process_request')
            ->with(
                $this->equalTo('partnerId'),
                $this->equalTo('apiKey'),
                $this->equalTo('apiUrl/v1/admin/partner/name'),
                $this->equalTo('DELETE'),
                $this->equalTo(null)
            );

        // Setup object in test
        $partner = new Netki\Partner('id', 'name');
        $partner->set_api_opts('apiKey', 'apiUrl', 'partnerId');
        $partner->set_requestor($this->processRequestMock);

        // Execute test
        $partner->delete();
    }
}
