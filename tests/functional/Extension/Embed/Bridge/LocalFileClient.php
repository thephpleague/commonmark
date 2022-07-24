<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Adapted from the embed/embed test suite,
 * (c) 2017 Oscar Otero Marzoa, used under the MIT license.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Functional\Extension\Embed\Bridge;

use Embed\Http\CurlClient;
use Embed\Http\FactoryDiscovery;
use League\CommonMark\Exception\IOException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * Decorator to cache requests into files
 */
final class LocalFileClient implements ClientInterface
{
    private string $path;
    private ResponseFactoryInterface $responseFactory;
    private ClientInterface $client;

    public function __construct(string $path)
    {
        $this->path            = $path;
        $this->responseFactory = FactoryDiscovery::getResponseFactory();
        $this->client          = new CurlClient($this->responseFactory);
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $uri      = $request->getUri();
        $filename = $this->path . '/' . self::getFilename($uri);

        if (\is_file($filename)) {
            $response = $this->readResponse($filename);
        } else {
            $response = $this->client->sendRequest($request);
            $this->saveResponse($response, $filename);
        }

        return $response;
    }

    public static function getFilename(UriInterface $uri): string
    {
        $query = $uri->getQuery();

        return \sprintf(
            '%s.%s%s.json',
            $uri->getHost(),
            \trim(\preg_replace('/[^\w.-]+/', '-', \strtolower($uri->getPath())) ?? '', '-'),
            $query ? '.' . \md5($uri->getQuery()) : ''
        );
    }

    private function readResponse(string $filename): ResponseInterface
    {
        $file = \file_get_contents($filename);
        if ($file === false) {
            throw new IOException(\sprintf('Unable to read file "%s"', $filename));
        }

        $message  = \json_decode($file, true, JSON_THROW_ON_ERROR);
        $response = $this->responseFactory->createResponse($message['statusCode'], $message['reasonPhrase']);

        foreach ($message['headers'] as $name => $value) {
            $response = $response->withHeader($name, $value);
        }

        $body = $response->getBody();
        $body->write($message['body']);
        $body->rewind();

        return $response;
    }

    private function saveResponse(ResponseInterface $response, string $filename): void
    {
        $message = [
            'headers' => $response->getHeaders(),
            'statusCode' => $response->getStatusCode(),
            'reasonPhrase' => $response->getReasonPhrase(),
            'body' => (string) $response->getBody(),
        ];

        \file_put_contents($filename, \json_encode($message, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
    }
}
