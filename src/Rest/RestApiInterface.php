<?php

declare(strict_types=1);

namespace Aedon\DiscordBot\Rest;

interface RestApiInterface
{
    /**
     * @param string $path
     * @return RestApiResponse
     */
    public function get(string $path): RestApiResponse;

    /**
     * @param string $path
     * @param mixed[] $data
     * @param string[] $additionalHeaders
     * @return RestApiResponse
     */
    public function post(string $path, array $data, array $additionalHeaders = []): RestApiResponse;

    /**
     * @param string $path
     * @param mixed[] $data
     * @param string[] $additionalHeaders
     * @return RestApiResponse
     */
    public function put(string $path, array $data, array $additionalHeaders = []): RestApiResponse;

    /**
     * @param string $path
     * @param mixed[] $data
     * @param string[] $additionalHeaders
     * @return RestApiResponse
     */
    public function patch(string $path, array $data, array $additionalHeaders = []): RestApiResponse;

    public function delete(string $path): RestApiResponse;

}