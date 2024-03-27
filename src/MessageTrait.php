<?php
namespace RingCentral\Psr7;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\MessageInterface;

/**
 * Trait implementing functionality common to requests and responses.
 */
abstract class MessageTrait
{
    /** @var array Cached HTTP header collection with lowercase key to values */
    protected $headers = array();

    /** @var array Actual key to list of values per header. */
    protected $headerLines = array();

    /** @var string */
    protected $protocol = '1.1';

    /** @var StreamInterface */
    protected $stream;

    public function getProtocolVersion(): string
    {
        return $this->protocol;
    }

    public function withProtocolVersion($version): MessageInterface
    {
        if ($this->protocol === $version) {
            return $this;
        }

        $new = clone $this;
        $new->protocol = $version;
        return $new;
    }

    public function getHeaders(): array
    {
        return $this->headerLines;
    }

    public function hasHeader($header): bool
    {
        return isset($this->headers[strtolower($header)]);
    }

    public function getHeader($header): array
    {
        $name = strtolower($header);
        return isset($this->headers[$name]) ? $this->headers[$name] : array();
    }

    public function getHeaderLine($header): string
    {
        return implode(', ', $this->getHeader($header));
    }

    public function withHeader($header, $value): MessageInterface
    {
        $new = clone $this;
        $header = trim($header);
        $name = strtolower($header);

        if (!is_array($value)) {
            $new->headers[$name] = array(trim($value));
        } else {
            $new->headers[$name] = $value;
            foreach ($new->headers[$name] as &$v) {
                $v = trim($v);
            }
        }

        // Remove the header lines.
        foreach (array_keys($new->headerLines) as $key) {
            if (strtolower($key) === $name) {
                unset($new->headerLines[$key]);
            }
        }

        // Add the header line.
        $new->headerLines[$header] = $new->headers[$name];

        return $new;
    }

    public function withAddedHeader($header, $value): MessageInterface
    {
        if (!$this->hasHeader($header)) {
            return $this->withHeader($header, $value);
        }

        $header = trim($header);
        $name = strtolower($header);

        $value = (array) $value;
        foreach ($value as &$v) {
            $v = trim($v);
        }

        $new = clone $this;
        $new->headers[$name] = array_merge($new->headers[$name], $value);
        $new->headerLines[$header] = array_merge($new->headerLines[$header], $value);

        return $new;
    }

    public function withoutHeader($header): MessageInterface
    {
        if (!$this->hasHeader($header)) {
            return $this;
        }

        $new = clone $this;
        $name = strtolower($header);
        unset($new->headers[$name]);

        foreach (array_keys($new->headerLines) as $key) {
            if (strtolower($key) === $name) {
                unset($new->headerLines[$key]);
            }
        }

        return $new;
    }

    public function getBody(): StreamInterface
    {
        if (!$this->stream) {
            $this->stream = stream_for('');
        }

        return $this->stream;
    }

    public function withBody(StreamInterface $body): MessageInterface
    {
        if ($body === $this->stream) {
            return $this;
        }

        $new = clone $this;
        $new->stream = $body;
        return $new;
    }

    protected function setHeaders(array $headers): void
    {
        $this->headerLines = $this->headers = array();
        foreach ($headers as $header => $value) {
            $header = trim($header);
            $name = strtolower($header);
            if (!is_array($value)) {
                $value = trim($value);
                $this->headers[$name][] = $value;
                $this->headerLines[$header][] = $value;
            } else {
                foreach ($value as $v) {
                    $v = trim($v);
                    $this->headers[$name][] = $v;
                    $this->headerLines[$header][] = $v;
                }
            }
        }
    }
}
