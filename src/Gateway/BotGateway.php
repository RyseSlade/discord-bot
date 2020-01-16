<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Gateway;

use Aedon\Expect;
use function curl_close;
use function curl_exec;
use function curl_init;
use function curl_setopt;
use function is_array;
use function is_string;
use function json_decode;

class BotGateway
{
    private const API_VERSION = 6;
    private const ENCODING = 'json';
    private const DISCORD_API = 'https://discordapp.com/api';

    private string $token;
    private int $version;
    private string $encoding;
    private string $discordApi;

    public function __construct(string $token, int $version = self::API_VERSION, string $encoding = self::ENCODING, string $discordApi = self::DISCORD_API)
    {
        $this->token = $token;
        $this->version = $version;
        $this->encoding = $encoding;
        $this->discordApi = $discordApi;
    }

    public function getUrl(): string
    {
        /** @var resource $curl */
        $curl = curl_init(self::DISCORD_API . '/gateway/bot');

        Expect::isNotFalse($curl);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Bot ' . $this->token]);

        $response = curl_exec($curl);

        curl_close($curl);

        $url = '';

        if (is_string($response) && !empty($response)) {
            $result = json_decode($response, true);

            if (is_array($result)) {
                $url = isset($result['url']) && is_string($result['url']) ? $result['url'] . '/?v=' . $this->version . '&encoding=' . $this->encoding : '';
            }
        }

        return $url;
    }
}