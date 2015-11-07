<?php
/**
 * Created by PhpStorm.
 * User: frank
 * Date: 11/5/15
 * Time: 2:33 PM
 */

namespace Netki;

/**
 * Netki Partner Domain Object
 *
 * @package Netki
 */
class Domain extends BaseObject
{
    public $name;
    public $status;
    public $delegationStatus;
    public $delegationMessage;
    public $walletNameCount;
    public $nextRoll;
    public $dsRecords;
    public $nameservers;
    public $publicSigningKey;

    /**
     * Instantiate Domain Object with name
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->dsRecords = array();
        $this->nameservers = array();

        parent::__construct();
    }

    /**
     * Call Domain Deletion on Netki API
     *
     * @throws \Exception For Any Non Success Case
     */
    public function delete()
    {
        $this->requestor->process_request(
            $this->partnerId,
            $this->apiKey,
            $this->apiUrl . '/v1/partner/domain/'. $this->name,
            'DELETE',
            null
        );
    }

    /**
     * Get Domain Status from Netki API
     *
     * @throws \Exception For Any Non Success Case
     */
    public function load_status()
    {
        $responseObj = $this->requestor->process_request(
            $this->partnerId,
            $this->apiKey,
            $this->apiUrl . '/v1/partner/domain/'. $this->name,
            'GET',
            null
        );

        $this->status = $responseObj->status;
        $this->delegationStatus = $responseObj->delegation_status;
        $this->delegationMessage = $responseObj->delegation_message;
        $this->walletNameCount = $responseObj->wallet_name_count;
    }

    /**
     * Get Domain DNSSEC Status from Netki API
     *
     * @throws \Exception For Any Non Success Case
     */
    public function load_dnssec_details()
    {
        $responseObj = $this->requestor->process_request(
            $this->partnerId,
            $this->apiKey,
            $this->apiUrl . '/v1/partner/domain/dnssec/'. $this->name,
            'GET',
            null
        );

        if (!empty($responseObj->public_key_signing_key))
        {
            $this->publicSigningKey = $responseObj->public_key_signing_key;
        }

        if (!empty($responseObj->ds_records))
        {
            $this->dsRecords = $responseObj->ds_records;
        }

        if (!empty($responseObj->nameservers))
        {
            $this->nameservers = $responseObj->nameservers;
        }

        if (!empty($responseObj->nextroll_date))
        {
            date_default_timezone_set('UTC');
            $this->nextRoll = \DateTime::createFromFormat('Y-m-d H:i:s', $responseObj->nextroll_date);
        }
    }
}