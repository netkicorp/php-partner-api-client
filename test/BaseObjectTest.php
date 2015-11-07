<?php

/**
 * Created by PhpStorm.
 * User: frank
 * Date: 11/5/15
 * Time: 6:54 PM
 */

class BaseObjectTest extends PHPUnit_Framework_TestCase
{
    public function testGettersSetters()
    {
        $baseObj = new Netki\BaseObject();
        $baseObj->set_api_opts('apiKey', 'apiUrl', 'partnerId');

        $this->assertEquals('apiKey', $baseObj->get_apiKey());
        $this->assertEquals('apiUrl', $baseObj->get_apiUrl());
        $this->assertEquals('partnerId', $baseObj->get_partnerId());
    }

}
