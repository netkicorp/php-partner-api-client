<?php

namespace Netki;

/**
 * Wallet Name data container
 *
 * @package Netki
 */
class WalletName extends BaseObject
{
    private $wallets;
    public $id;
    public $domainName;
    public $name;
    public $externalId;

    /**
     * Instantiate WalletName object
     *
     * @param string $domainName
     * @param string $name
     * @param string $externalId
     * @param string $id
     * @param array $wallets
     */
    public function __construct($domainName, $name, $externalId, $id = null, $wallets = array())
    {
        $this->domainName = $domainName;
        $this->name = $name;
        $this->externalId = $externalId;
        $this->id = $id;
        $this->wallets = $wallets;

        parent::__construct();
    }

    /**
     * Get WalletName used currencies
     *
     * @return array
     */
    public function get_used_currencies()
    {
        return array_keys($this->wallets);
    }

    /**
     * Get WalletName wallet address for specified currency shortcode
     *
     * @param string $currency shortcode (i.e, btc, ltc, nmc, etc)
     * @return string
     */
    public function get_wallet_address($currency)
    {
        return $this->wallets[$currency];
    }

    /**
     * Set Wallet Address for specified currency shortcode
     *
     * @param string $currency
     * @param string $walletAddress
     */
    public function set_currency_address($currency, $walletAddress)
    {
        $this->wallets[$currency] = $walletAddress;
    }

    /**
     * Remove Wallet Address for specified currency shortcode
     *
     * @param string $currency
     */
    public function remove_currency_address($currency)
    {
        unset($this->wallets[$currency]);
    }

    /**
     * Save WalletName to Netki API
     *
     * @throws \Exception Occurs on Bad HTTP Request / Response
     */
    public function save()
    {
        $walletData = array();
        $walletData['name'] = $this->name;
        $walletData['domain_name'] = $this->domainName;
        $walletData['external_id'] = $this->externalId;
        $walletData['wallets'] = array();

        if (!empty($this->id))
        {
            $walletData['id'] = $this->id;
        }

        foreach(array_keys($this->wallets) as $currency)
        {
            $walletData['wallets'][] = array('currency' => $currency, 'wallet_address' => $this->wallets[$currency]);
        }

        $fullRequest = array('wallet_names' => array($walletData));

        if (!empty($this->id))
        {
            $this->requestor->process_request(
                $this->partnerId,
                $this->apiKey,
                $this->apiUrl . '/v1/partner/walletname',
                'PUT',
                $fullRequest
            );
        } else {
            $retVal = $this->requestor->process_request(
                $this->partnerId,
                $this->apiKey,
                $this->apiUrl . '/v1/partner/walletname',
                'POST',
                $fullRequest
            );

            if (isset($retVal->wallet_names))
            {
                foreach($retVal->wallet_names as $wn)
                {
                    if ($wn->domain_name == $this->domainName && $wn->name == $this->name)
                    {
                        $this->id = $wn->id;
                    }
                }
            }
        }
    }

    /**
     * Delete WalletName via Netki API
     *
     * @throws \Exception Occurs on Bad HTTP Request / Response
     */
    public function delete()
    {
        if (empty($this->id))
        {
            throw new \Exception ('Unable to Delete Object that Does Not Exist Remotely');
        }

        $walletData = array();
        $walletData['domain_name'] = $this->domainName;
        $walletData['id'] = $this->id;

        $fullRequest = array('wallet_names' => array($walletData));

        $this->requestor->process_request(
            $this->partnerId,
            $this->apiKey,
            $this->apiUrl . '/v1/partner/walletname/' . $this->domainName . '/' . $this->id,
            'DELETE',
            null
        );
    }
}
