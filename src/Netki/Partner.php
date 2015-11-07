<?php
/**
 * Created by PhpStorm.
 * User: frank
 * Date: 11/5/15
 * Time: 1:35 PM
 */

namespace Netki;

/**
 * Class Partner
 *
 * @package Netki
 */
class Partner extends BaseObject
{
    public $id;
    public $name;

    /**
     * Instantiate a Partner object
     *
     * @param string $id
     * @param string $name
     */
    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;

        parent::__construct();
    }

    /**
     * Delete partner via Netki API
     *
     * @throws \Exception For Any Non Success Case
     */
    public function delete()
    {
        $this->requestor->process_request(
            $this->partnerId,
            $this->apiKey,
            $this->apiUrl . '/v1/admin/partner/' . $this->name,
            'DELETE',
            null
        );
    }
}