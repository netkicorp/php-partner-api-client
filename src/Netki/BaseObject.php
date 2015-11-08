<?php

namespace Netki;

/**
 * Superclass used for all Netki data containers
 *
 * @package Netki
 */
class BaseObject
{
    protected $apiKey;
    protected $apiUrl;
    protected $partnerId;
    protected $requestor;

    /**
     * BaseObject constructor.
     */
    public function __construct()
    {
        $this->requestor = new Request();
    }

    /**
     * @param $requestor
     */
    public function set_requestor($requestor)
    {
        $this->requestor = $requestor;
    }

    /**
     * Set Data API Information for CRUD operations
     *
     * @param string $apiKey
     * @param string $apiUrl
     * @param string $partnerId
     */
    public function set_api_opts($apiKey, $apiUrl, $partnerId)
    {
        $this->apiKey = $apiKey;
        $this->apiUrl = $apiUrl;
        $this->partnerId = $partnerId;
    }

    /**
     * Get Netki Partner API Key
     *
     * @return string
     */
    public function get_apiKey() { return $this->apiKey; }

    /**
     * Get Netki Partner API Url Base
     *
     * @return string
     */
    public function get_apiUrl() { return $this->apiUrl; }

    /**
     * Get Netki Partner ID
     *
     * @return string
     */
    public function get_partnerId() { return $this->partnerId; }
}