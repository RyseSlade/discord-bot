<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Rest;

use RuntimeException;
use function curl_close;
use function curl_exec;
use function curl_getinfo;
use function curl_init;
use function curl_setopt;
use function is_numeric;
use function json_decode;
use function json_encode;
use function mb_substr;
use function strtolower;

class RestApi implements RestApiInterface
{
    public const DISCORD_API = 'https://discordapp.com/api';
    public const API_VERSION = 6;
    private const BOT_URL = 'https://github.com/RyseSlade/discord-bot';
    private const BOT_VERSION = '0.7.0';

    private string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function get(string $path): RestApiResponse
    {
        return $this->send($path, [], 'GET');
    }

    public function post(string $path, array $data, array $additionalHeaders = []): RestApiResponse
    {
        return $this->send($path, $data, 'POST', $additionalHeaders);
    }

    public function put(string $path, array $data, array $additionalHeaders = []): RestApiResponse
    {
        return $this->send($path, $data, 'PUT', $additionalHeaders);
    }

    public function patch(string $path, array $data, array $additionalHeaders = []): RestApiResponse
    {
        return $this->send($path, $data, 'PATCH', $additionalHeaders);
    }

    public function delete(string $path): RestApiResponse
    {
        return $this->send($path, [], 'DELETE');
    }

    private function send(string $path, array $data, string $method, array $additionalHeaders = []): RestApiResponse
    {
        if (mb_substr($path, 0, 1) !== '/') {
            $path = '/' . $path;
        }

        $curl = curl_init(self::DISCORD_API . '/v' . self::API_VERSION . $path);

        if ($curl === false) {
            throw new RuntimeException('Could not initialize curl request');
        }

        $headers = [
            'Authorization: Bot ' . $this->token,
            'Content-Type: application/json',
            'User-Agent: Aedon Discord Bot (' . self::BOT_URL . ', ' . self::BOT_VERSION . ')',
        ];

        $headers += $additionalHeaders;

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        if (!empty($data)) {
            $content = json_encode($data);

            if ($content === false) {
                throw new RuntimeException('Could not json encode data array');
            }

            curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
        }

        $result = curl_exec($curl);
        $info = null;

        $responseHttpCode = null;
        $responseData = null;

        if (is_string($result)) {
            $info = curl_getinfo($curl);

            $responseHttpCode = isset($info['http_code']) && is_numeric($info['http_code']) ? (int)$info['http_code'] : null;

            if (!empty($result) && isset($info['content_type']) && strtolower($info['content_type']) == 'application/json') {
                $jsonData = json_decode($result, true);

                if ($jsonData !== null) {
                    $responseData = $jsonData;
                }
            }
        }

        curl_close($curl);

        return new RestApiResponse($responseHttpCode, $responseData);
    }
}