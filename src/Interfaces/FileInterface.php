<?php

namespace duncan3dc\MetaAudio\Interfaces;

use duncan3dc\MetaAudio\Exceptions\ReadException;
use duncan3dc\MetaAudio\Exceptions\WriteException;

/**
 * A custom file handler.
 */
interface FileInterface
{

    /**
     * Get the path including the filename.
     *
     * @return string
     */
    public function getFullPath(): string;


    /**
     * @return void
     */
    public function rewind(): void;


    /**
     * Move the internal pointer through the file.
     *
     * @param int $offset The number of bytes to seek by, a negative value can be used to move backwards
     *
     * @return void
     */
    public function seek(int $offset): void;


    /**
     * Seek to a specific position from the start of the file.
     *
     * @param int $offset The number of bytes to seek by
     *
     * @return void
     */
    public function seekFromStart(int $offset): void;


    /**
     * Seek to a specific position from the end of the file.
     *
     * @param int $offset The number of bytes to seek from the end (must be negative)
     *
     * @return void
     */
    public function seekFromEnd(int $offset): void;


    /**
     * @return bool
     */
    public function eof(): bool;


    /**
     * @param int $bytes
     *
     * @return string
     * @throws ReadException
     */
    public function read(int $bytes): string;


    /**
     * Get the rest of the file's contents from the current position.
     *
     * @return string
     */
    public function readAll(): string;


    /**
     * Get the current internal pointer's position.
     *
     * @return int
     * @throws ReadException
     */
    public function getCurrentPosition(): int;


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


    /**
     * Process the remainder of the file from the current position through the callback.
     *
     * @param callable $func A function that takes a single string parameter (which will contain each chunk of the file read)
     *
     * @return void
     */
    public function readNextCallback(callable $func): void;


    /**
     * Process the previous contents of the file from the current position through the callback.
     *
     * @param callable $func A function that takes a single string parameter (which will contain each chunk of the file read in reverse)
     *
     * @return void
     */
    public function readPreviousCallback(callable $func): void;


    /**
     * Get the position of the next occurrence of a string from the current position.
     *
     * @param string $string The string to search for
     *
     * @return int|null Either the position of the string or null if it doesn't exist
     */
    public function getNextPosition(string $string): ?int;


    /**
     * Get the position of the previous occurrence of a string from the current position.
     *
     * @param string $string The string to search for
     *
     * @return int|null Either the position of the string or null if it doesn't exist
     */
    public function getPreviousPosition(string $string): ?int;
}
