<?php

namespace AlexisRiot\Yousign;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class Yousign
{
    /**
     * @const string
     */
    const BASE_URI = [
        'production' => "https://api.yousign.com",
        'staging' => "https://staging-api.yousign.com",
    ];

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $apiEnv;

    public function __construct()
    {
        $this->setApiKey(config('yousign.api_key'));
        $this->setApiEnv(config('yousign.api_env'));

        $this->client = new Client([
            'base_uri' => $this->getBaseURL(),
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * @return string
     */
    protected function getBaseURL()
    {
        return self::BASE_URI[$this->apiEnv];
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     */
    protected function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }


    /**
     * @param string $apiEnv
     */
    public function setApiEnv($apiEnv)
    {
        $this->apiEnv = $apiEnv;
    }

    /**
     * @return string
     */
    protected function getApiEnv()
    {
        return $this->apiEnv;
    }

    /**
     * Make request.
     *
     * @param string $method
     * @param string $uri
     * @param null|array $query
     * @param null|array $params
     * @return array
     */
    protected function makeRequest(string $method, string $uri, array $query = [], array $params = [])
    {
        try {
            $response = $this->client->request($method, $uri, ['query' => $query, 'body' => json_encode($params)]);
            return json_decode((string) $response->getBody(), true);
        } catch(GuzzleException $e) {
            $response = json_decode($e->getResponse()->getBody()->getContents(), true);

            dd($response);
            Log::error($e->getMessage());
        }
    }

    /**
     * Lists all users.
     *
     * @param null|array $query
     * @return array
     */
    public function getUsers()
    {
        return $this->makeRequest('get', 'users');
    }

    /**
     * Send file for procedure.
     *
     * @param null|array $query
     * @return array
     */
    public function createFile(array $params = [])
    {
        return $this->makeRequest('POST', 'files', [], $params);
    }

    /**
     * Lists all users.
     *
     * @param null|array $query
     * @return array
     */
    public function createBasicProcedure(array $params = [])
    {
        return $this->makeRequest('post', 'procedures', [], $params);
    }

    /**
     * Lists all users.
     *
     * @param string $memberId
     * @return string
     */
    private function formatMemberId(string $memberId) {
        return str_replace("/members/", "", $memberId);
    }

    /**
     * Create an SMS operation that will send an SMS to the member with the code.
     *
     * @param null|array $query
     * @return array
     */
    public function createOperationSMS($memberId)
    {
        return $this->makeRequest('post', 'operations', [], [
            "mode" => "sms",
            "type" => "accept",
            "members" => [
                "/members/{$this->formatMemberId($memberId)}",
            ],
            "metadata" => [],
        ]);
    }

    /**
     * Validate the SMS code received.
     *
     * @param null|array $query
     * @return array
     */
    public function authenticateSMS($memberId, $code)
    {
        return $this->makeRequest('put', "authentications/sms/{$this->formatMemberId($memberId)}", [], [
            "code" => $code,
        ]);
    }
}
