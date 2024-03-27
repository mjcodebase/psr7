<?php
namespace RingCentral\Psr7;

use Psr\Http\Message\StreamInterface;

/**
 * Stream decorator that prevents a stream from being seeked
 */
class NoSeekStream extends StreamDecoratorTrait implements StreamInterface
{

    public function seek($offset, $whence = SEEK_SET): void
    {
        throw new \RuntimeException('Cannot seek a NoSeekStream');
    }

    public function isSeekable(): bool
    {
        return false;
    }
}
