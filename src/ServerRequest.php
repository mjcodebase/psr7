<?php

namespace RingCentral\Psr7;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;
use RingCentral\Psr7\Request;

/**
 * PSR-7 server-side request implementation.
 */
class ServerRequest extends Request implements ServerRequestInterface
{
    private $attributes = array();

    private $serverParams = array();
    private $fileParams = array();
    private $cookies = array();
    private $queryParams = array();
    private $parsedBody = null;

    /**
     * @param null|string $method HTTP method for the request.
     * @param null|string|UriInterface $uri URI for the request.
     * @param array $headers Headers for the message.
     * @param string|resource|StreamInterface $body Message body.
     * @param string $protocolVersion HTTP protocol version.
     * @param array $serverParams Server params of the request.
     *
     * @throws InvalidArgumentException for an invalid URI
     */
    public function __construct(
        $method,
        $uri,
        array $headers = array(),
        $body = null,
        $protocolVersion = '1.1',
        $serverParams = array()
    ) {
        parent::__construct($method, $uri, $headers, $body, $protocolVersion);
        $this->serverParams = $serverParams;
    }

    public function getServerParams(): array
    {
        return $this->serverParams;
    }

    public function getCookieParams(): array
    {
        return $this->cookies;
    }

    public function withCookieParams(array $cookies): ServerRequestInterface
    {
        $new = clone $this;
        $new->cookies = $cookies;
        return $new;
    }

    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    public function withQueryParams(array $query): ServerRequestInterface
    {
        $new = clone $this;
        $new->queryParams = $query;
        return $new;
    }

    public function getUploadedFiles(): array
    {
        return $this->fileParams;
    }

    public function withUploadedFiles(array $uploadedFiles): ServerRequestInterface
    {
        $new = clone $this;
        $new->fileParams = $uploadedFiles;
        return $new;
    }

    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    public function withParsedBody($data): ServerRequestInterface
    {
        $new = clone $this;
        $new->parsedBody = $data;
        return $new;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getAttribute($name, $default = null)
    {
        if (!array_key_exists($name, $this->attributes)) {
            return $default;
        }
        return $this->attributes[$name];
    }

    public function withAttribute($name, $value): ServerRequestInterface
    {
        $new = clone $this;
        $new->attributes[$name] = $value;
        return $new;
    }

    public function withoutAttribute($name): ServerRequestInterface
    {
        $new = clone $this;
        unset($new->attributes[$name]);
        return $new;
    }
}
