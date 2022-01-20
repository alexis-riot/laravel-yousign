<?php

namespace AlexisRiot\Yousign;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class Yousign
{
    /**
     * @const array
     */
    const API_URL = [
        'production' => 'https://api.yousign.com',
        'staging' => 'https://staging-api.yousign.com',
    ];

    /**
     * @const array
     */
    const APP_URL = [
        'production' => 'https://app.yousign.com',
        'staging' => 'https://staging-app.yousign.com',
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
        return self::API_URL[$this->apiEnv];
    }

    /**
     * @return string
     */
    protected function getBaseAppURL()
    {
        return self::APP_URL[$this->apiEnv];
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param  string $apiKey
     */
    protected function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @param  string $apiEnv
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
     * @param  string $method
     * @param  string $uri
     * @param  null|array $query
     * @param  null|array $params
     * @return array
     */
    protected function makeRequest(string $method, string $uri, array $query = [], array $params = [])
    {
        try {
            $response = $this->client->request($method, $uri, ['query' => $query, 'body' => json_encode($params)]);

            if ($response->getHeaderLine('Content-Type') === 'application/pdf') {
                return $response->getBody()->getContents();
            }

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * Lists all users.
     *
     * @param  null|array $query
     * @return array
     */
    public function getUsers()
    {
        return $this->makeRequest('get', 'users');
    }

    /**
     * Send file for procedure.
     *
     * @param  null|array $query
     * @return array
     */
    public function createFile(array $params = [])
    {
        return $this->makeRequest('POST', 'files', [], $params);
    }

    /**
     * Create a basic procedure.
     *
     * @param  null|array $query
     * @return array
     */
    public function createBasicProcedure(array $params = [])
    {
        return $this->makeRequest('post', 'procedures', [], $params);
    }

    /**
     * Format a member ID.
     *
     * @param  string $memberId
     * @return string
     */
    private function formatMemberId(string $memberId)
    {
        return str_replace('/members/', '', $memberId);
    }

    /**
     * Format a file ID.
     *
     * @param  string $fileId
     * @return string
     */
    private function formatFileId(string $fileId)
    {
        return str_replace('/files/', '', $fileId);
    }

    /**
     * Create an SMS operation that will send an SMS to the member with the code.
     *
     * @param  null|array $query
     * @return array
     */
    public function createOperationSMS($memberId)
    {
        return $this->makeRequest('post', 'operations', [], [
            'mode' => 'sms',
            'type' => 'accept',
            'members' => [
                "/members/{$this->formatMemberId($memberId)}",
            ],
            'metadata' => [],
        ]);
    }

    /**
     * Create an Email operation that will send an email to the member with the code.
     *
     * @param  null|array $query
     * @return array
     */
    public function createOperationEmail($memberId)
    {
        return $this->makeRequest('post', 'operations', [], [
            'mode' => 'email',
            'type' => 'accept',
            'members' => [
                "/members/{$this->formatMemberId($memberId)}",
            ],
            'metadata' => [],
        ]);
    }

    /**
     * Validate the SMS code received.
     *
     * @param  null|array $query
     * @return array
     */
    public function authenticateSMS($memberId, $code)
    {
        return $this->makeRequest('put', "authentications/sms/{$this->formatMemberId($memberId)}", [], [
            'code' => $code,
        ]);
    }

    /**
     * Validate the Email code received.
     *
     * @param  null|array $query
     * @return array
     */
    public function authenticateEmail($memberId, $code)
    {
        return $this->makeRequest('put', "authentications/email/{$this->formatMemberId($memberId)}", [], [
            'code' => $code,
        ]);
    }

    /**
     * Download the signed file.
     *
     * @param  string $fileId
     * @param  bool $media
     * @return mixed
     */
    public function downloadFile($fileId, $media = false)
    {
        $query = [];

        if ($media) {
            $query['alt'] = 'media';
        }

        return $this->makeRequest('get', "files/{$this->formatFileId($fileId)}/download", $query);
    }

    /**
     * Download the signed file.
     *
     * @param  string $fileId
     * @return array
     */
    public function getFileInfo($fileId)
    {
        return $this->makeRequest('get', "files/{$this->formatFileId($fileId)}");
    }

    /**
     * Get the iframe URL
     *
     * @param  string $memberId
     * @return array
     */
    public function getIframeURL($memberId)
    {
        return $this->getBaseAppURL()."/procedure/sign?members={$memberId}";
    }
}
