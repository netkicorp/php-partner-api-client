<?php

namespace Netki;

/**
 * Netki Partner Client
 *
 * @package Netki
 */
class NetkiClient
{
    private $partnerId;
    private $apiKey;
    private $apiUrl = 'https://api.netki.com';
    private $lookupUrl = 'https://pubapi.netki.com/api/wallet_lookup/';
    private $requestor;

    /**
     * Instantiate a NetkiClient object
     *
     * @param string $partnerId
     * @param string $apiKey
     * @param string $apiUrl
     * @param optional string $lookupUrl
     */
    public function __construct($partnerId, $apiKey, $apiUrl, $lookupUrl = null)
    {
        $this->partnerId = $partnerId;
        $this->apiKey = $apiKey;
        $this->apiUrl = empty($apiUrl) ? $this->apiUrl : $apiUrl;
        $this->lookupUrl = empty($lookupUrl) ? $this->lookupUrl : $lookupUrl;
        $this->requestor = new Request();
    }

    /**
     * @param Request $requestor
     */
    public function set_requestor($requestor)
    {
        $this->requestor = $requestor;
    }

    /**
     * Get Wallet Names matching the given criteria
     *
     * @param string $domainName Domain Name filter
     * @param string $externalId External ID filter
     * @return array Array of Wallet Names
     * @throws \Exception For Any Non Success Case
     */
    public function get_wallet_names($domainName = null, $externalId = null)
    {
        $results = array();

        $args = array();
        if (!empty($domainName))
        {
            $args[] = 'domain_name=' . $domainName;
        }

        if (!empty($externalId))
        {
            $args[] = 'external_id=' . urlencode($externalId);
        }

        $uri = $this->apiUrl . "/v1/partner/walletname";
        if (count($args) > 0)
        {
            $uri = $uri . '?' . join('&', $args);
        }

        $responseData = $this->requestor->process_request(
            $this->partnerId,
            $this->apiKey,
            $uri,
            'GET',
            null
        );

        if ($responseData->wallet_name_count == 0)
        {
            return $results;
        }

        foreach($responseData->wallet_names as $wallet_name)
        {
            $wn = new WalletName(
                $wallet_name->domain_name,
                $wallet_name->name,
                $wallet_name->external_id,
                $wallet_name->id
            );

            foreach($wallet_name->wallets as $wallet)
            {
                $wn->set_currency_address($wallet->currency, $wallet->wallet_address);
            }

            $wn->set_api_opts($this->apiKey, $this->apiUrl, $this->partnerId);
            $wn->set_requestor($this->requestor);
            $results[] = $wn;
        }
        return $results;
    }

    /**
     * Create New WalletName object
     *
     * @param string $domainName
     * @param string $name
     * @param string $externalId
     * @return WalletName
     */
    public function create_wallet_name($domainName, $name, $externalId)
    {
        $wn = new WalletName(
            $domainName,
            $name,
            $externalId
        );
        $wn->set_api_opts($this->apiKey, $this->apiUrl, $this->partnerId);
        $wn->set_requestor($this->requestor);
        return $wn;
    }

    /*
     * Partner Operations
     */

    /**
     * Create a new Partner
     *
     * @param string $partnerName
     * @return Partner
     * @throws \Exception For Any Non Success Case
     */
    public function create_partner($partnerName)
    {
        $responseObj = $this->requestor->process_request(
            $this->partnerId,
            $this->apiKey,
            $this->apiUrl . '/v1/admin/partner/' . $partnerName,
            'POST',
            null
        );

        $partner = new Partner($responseObj->partner->id, $responseObj->partner->name);
        $partner->set_api_opts($this->apiKey, $this->apiUrl, $this->partnerId);
        $partner->set_requestor($this->requestor);

        return $partner;
    }

    /**
     * Get all partners
     *
     * @return array Array of Partners
     * @throws \Exception For Any Non Success Case
     */
    public function get_partners()
    {
        $partners = Array();

        $responseObj = $this->requestor->process_request(
            $this->partnerId,
            $this->apiKey,
            $this->apiUrl . '/v1/admin/partner',
            'GET',
            null
        );

        if (!isset($responseObj->partners))
        {
            return $partners;
        }

        foreach($responseObj->partners as $partner)
        {
            $p = new Partner(
                $partner->id,
                $partner->name
            );
            $p->set_api_opts($this->apiKey, $this->apiUrl, $this->partnerId);
            $p->set_requestor($this->requestor);
            $partners[] = $p;
        }
        return $partners;
    }

    /*
     * Domain Operations
     */

    /**
     * Create and save a new Domain with Netki
     *
     * @param string $domainName
     * @param string $partner
     * @return Domain
     * @throws \Exception For Any Non Success Case
     */
    public function create_domain($domainName, $partner = null)
    {
        $submitData = null;

        if (!empty($partner))
        {
            $submitData = array('partner_id' => $partner);
        }

        $responseObj = $this->requestor->process_request(
            $this->partnerId,
            $this->apiKey,
            $this->apiUrl . '/v1/partner/domain/' . $domainName,
            'POST',
            $submitData
        );

        $domain = new Domain($responseObj->domain_name);
        $domain->set_api_opts($this->apiKey, $this->apiUrl, $this->partnerId);
        $domain->set_requestor($this->requestor);
        $domain->status = $responseObj->status;
        foreach($responseObj->nameservers as $nameserver)
        {
            $domain->nameservers[] = $nameserver;
        }
        return $domain;
    }

    /**
     * Get all partner domains
     *
     * @return array
     * @throws \Exception For Any Non Success Case
     */
    public function get_domains()
    {
        $domains = array();

        $responseObj = $this->requestor->process_request(
            $this->partnerId,
            $this->apiKey,
            $this->apiUrl . '/api/domain',
            'GET',
            null
        );

        if (!isset($responseObj->domains))
        {
            return $domains;
        }

        foreach($responseObj->domains as $domain)
        {
            $d = new Domain($domain->domain_name);
            $d->set_api_opts($this->apiKey, $this->apiUrl, $this->partnerId);
            $d->set_requestor($this->requestor);
            $d->load_status();
            $d->load_dnssec_details();
            $domains[] = $d;
        }

        return $domains;
    }

    /**
     * Lookup Any Wallet Name
     *
     * @param string $walletName
     * @param string $currency
     *
     * @return string JSON
     * @throws \Exception For Any Non Success Case
     */
    public function lookup_wallet_name($walletName, $currency)
    {
        return $this->requestor->process_request(null, null, $this->lookupUrl . $walletName . '/' . $currency, 'GET', null);
    }

    /**
     * Lookup Supported Currencies For Any Wallet Name
     *
     * @param string $walletName
     *
     * @return string JSON
     * @throws \Exception For Any Non Success Case
     */
    public function get_wallet_name_currencies($walletName)
    {
        return $this->requestor->process_request(null, null, $this->lookupUrl . $walletName . '/available_currencies', 'GET', null);
    }

}