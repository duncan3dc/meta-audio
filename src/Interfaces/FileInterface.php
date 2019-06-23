<?php

namespace duncan3dc\MetaAudio\Interfaces;

use duncan3dc\MetaAudio\Exceptions\WriteException;

interface FileInterface extends ReadOnlyFileInterface
{


    /**
     * Truncate the file down to the given number of bytes.
     *
     * @param int $bytes
     *
     * @return void
     * @throws WriteException
     */
    public function truncate(int $bytes = 0): void;


    /**
     * @param string $data
     *
     * @return void
     * @throws WriteException
     */
    public function write(string $data): void;
}
