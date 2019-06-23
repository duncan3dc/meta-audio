<?php

namespace duncan3dc\MetaAudio;

use duncan3dc\MetaAudio\Exceptions\WriteException;
use duncan3dc\MetaAudio\Interfaces\FileInterface;

final class File implements FileInterface
{
    /** @var ReadOnly\File $file */
    private $file;


    /**
     * @param string $filename The filename to open
     *
     * @return self
     */
    public static function fromPath(string $filename): self
    {
        $file = new \SplFileObject($filename, "r+");
        return new self($file);
    }


    /**
     * @internal
     *
     * @param \SplFileObject $file
     */
    public function __construct(\SplFileObject $file)
    {
        $this->file = new ReadOnly\File($file);
    }


    /**
     * @inheritdoc
     */
    public function getFullPath(): string
    {
        return $this->file->getFullPath();
    }


    /**
     * @inheritdoc
     */
    public function rewind(): void
    {
        $this->file->rewind();
    }


    /**
     * @inheritdoc
     */
    public function seek(int $offset): void
    {
        $this->file->seek($offset);
    }


    /**
     * @inheritdoc
     */
    public function seekFromStart(int $offset): void
    {
        $this->file->seekFromStart($offset);
    }


    /**
     * @inheritdoc
     */
    public function seekFromEnd(int $offset): void
    {
        $this->file->seekFromEnd($offset);
    }


    /**
     * @inheritdoc
     */
    public function eof(): bool
    {
        return $this->file->eof();
    }


    /**
     * @inheritdoc
     */
    public function read(int $bytes): string
    {
        return $this->file->read($bytes);
    }


    /**
     * @inheritdoc
     */
    public function readAll(): string
    {
        return $this->file->readAll();
    }


    /**
     * @inheritdoc
     */
    public function getCurrentPosition(): int
    {
        return $this->file->getCurrentPosition();
    }


    /**
     * @inheritdoc
     */
    public function readNextCallback(callable $func): void
    {
        $this->file->readNextCallback($func);
    }


    /**
     * @inheritdoc
     */
    public function readPreviousCallback(callable $func): void
    {
        $this->file->readPreviousCallback($func);
    }


    /**
     * @inheritdoc
     */
    public function getNextPosition(string $string): ?int
    {
        return $this->file->getNextPosition($string);
    }


    /**
     * @inheritdoc
     */
    public function getPreviousPosition(string $string): ?int
    {
        return $this->file->getPreviousPosition($string);
    }


    /**
     * @inheritdoc
     */
    public function truncate(int $bytes = 0): void
    {
        $result = $this->file->getFile()->ftruncate($bytes);

        if ($result === false) {
            throw new WriteException("Unable to truncate the file");
        }
    }


    /**
     * @inheritdoc
     */
    public function write(string $data): void
    {
        $result = $this->file->getFile()->fwrite($data);

        if ($result === false) {
            throw new WriteException("Unable to write to the file");
        }
    }
}
