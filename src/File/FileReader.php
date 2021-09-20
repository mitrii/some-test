<?php

namespace App\File;

use React\Stream\ReadableStreamInterface;
use React\Stream\WritableStreamInterface;

class FileReader implements ReadableStreamInterface
{

    public function on($event, callable $listener)
    {
        // TODO: Implement on() method.
    }

    public function once($event, callable $listener)
    {
        // TODO: Implement once() method.
    }

    public function removeListener($event, callable $listener)
    {
        // TODO: Implement removeListener() method.
    }

    public function removeAllListeners($event = null)
    {
        // TODO: Implement removeAllListeners() method.
    }

    public function listeners($event = null)
    {
        // TODO: Implement listeners() method.
    }

    public function emit($event, array $arguments = [])
    {
        // TODO: Implement emit() method.
    }

    public function isReadable()
    {
        // TODO: Implement isReadable() method.
    }

    public function pause()
    {
        // TODO: Implement pause() method.
    }

    public function resume()
    {
        // TODO: Implement resume() method.
    }

    public function pipe(WritableStreamInterface $dest, array $options = array())
    {
        // TODO: Implement pipe() method.
    }

    public function close()
    {
        // TODO: Implement close() method.
    }
}
