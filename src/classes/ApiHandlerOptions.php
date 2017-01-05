<?php
/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 04.01.17
 * Time: 13:22
 */

class ApiHandlerOptions
{
    /**
     * @var string
     */
    private $apiUrl = "";

    /**
     * @var string
     */
    private $apiKey = "";

    /**
     * ApiHandlerOptions constructor.
     * @param string $apiUrl
     * @param string $apiKey
     */
    function __construct(string $apiUrl, string $apiKey)
    {
        $this->apiUrl = $apiUrl;
        $this->apiKey = $apiKey;
    }

    /**
     * @return string
     */
    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }
}