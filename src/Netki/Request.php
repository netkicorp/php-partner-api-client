<?php
/**
 * Created by PhpStorm.
 * User: frank
 * Date: 11/3/15
 * Time: 5:44 PM
 */

namespace Netki;

/**
 * Make and process HTTP calls to the Netki API
 *
 * @package Netki
 */
class Request
{
    /**
     * Process Netki API request and response
     *
     * @param string $partnerId
     * @param string $apiKey
     * @param string $apiURL
     * @param string $method
     * @param array $data
     * @return array|mixed
     * @throws \Exception
     */

    public function process_request($partnerId, $apiKey, $apiURL, $method, $data)
    {
        $supportedMethods = array('GET', 'PUT', 'POST', 'DELETE');
        $successCodes = array(200, 201, 202, 204);

        if (!in_array($method, $supportedMethods))
        {
            throw new \Exception('Unsupported method: ' . $method);
        }

        $headers = array('content-type' => 'application/json', 'X-Partner-ID' => $partnerId, 'Authorization' => $apiKey);
        $postData = !empty($data) ? json_encode($data) : null;

        $response = \Requests::request($apiURL, $headers, $postData, $method);

        if ($method == 'DELETE' && $response->status_code == 204)
        {
            return array();
        }

        if ($response->headers->getValues('content-type')[0] != 'application/json')
        {
            throw new \Exception('HTTP Response Contains Invalid Content-Type: ' .
                $response->headers->getValues('content-type')[0]);
        }

        $responseData = json_decode($response->body);

        if (empty($responseData))
        {
            throw new \Exception('Error parsing JSON Data');
        }

        if (!$responseData->success || !in_array($response->status_code, $successCodes))
        {
            $errorMessage = $responseData->message;

            if (isset($responseData->failures))
            {
                $errorMessage .= ' [FAILURES: ';
                $failures = array();

                foreach($responseData->failures as $failure)
                {
                    array_push($failures, $failure->message);
                }

                $errorMessage .= join(', ', $failures) . ']';
            }

            throw new \Exception('Request Failed: ' . $errorMessage);
        }

        return $responseData;
    }
}


