<?php

namespace duncan3dc\MetaAudio\ReadOnly;

use duncan3dc\MetaAudio\Exceptions\ReadException;
use duncan3dc\MetaAudio\Interfaces\ReadOnlyFileInterface;
use function strlen;
use function strpos;
use function strrpos;

final class File implements ReadOnlyFileInterface
{
    /** @var int An internal value to represent we should stop looping */
    private const CALLBACK_STOP = 408;

    /** @var int The amount of data to read from files in one go */
    private const BUFFER_SIZE = 32768;

    /** @Var SplFileObject $file */
    private $file;


    /**
     * @param string $filename The filename to open
     *
     * @return self
     */
    public static function fromPath(string $filename): self
    {
        $file = new \SplFileObject($filename, "r");
        return new self($file);
    }


    /**
     * @internal
     *
     * @param \SplFileObject $file
     */
    public function __construct(\SplFileObject $file)
    {
        $this->file = $file;
    }


    /**
     * @internal
     *
     * @return \SplFileObject
     */
    public function getFile(): \SplFileObject
    {
        return $this->file;
    }


    /**
     * @inheritdoc
     */
    public function getFullPath(): string
    {
        return $this->file->getPath() . "/" . $this->file->getFilename();
    }


    /**
     * @inheritdoc
     */
    public function rewind(): void
    {
        $this->file->fseek(0, \SEEK_SET);
    }


    /**
     * @inheritdoc
     */
    public function seek(int $offset): void
    {
        $this->file->fseek($offset, \SEEK_CUR);
    }


    /**
     * @inheritdoc
     */
    public function seekFromStart(int $offset): void
    {
        $this->file->fseek($offset, \SEEK_SET);
    }


    /**
     * @inheritdoc
     */
    public function seekFromEnd(int $offset): void
    {
        $this->file->fseek($offset, \SEEK_END);
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
        $data = $this->file->fread($bytes);

        if ($data === false) {
            throw new ReadException("Unable to read from the file");
        }

        return $data;
    }


    /**
     * @inheritdoc
     */
    public function readAll(): string
    {
        $contents = "";

        $this->readNextCallback(function ($data) use (&$contents) {
            $contents .= $data;
        });

        return $contents;
    }


    /**
     * @inheritdoc
     */
    public function getCurrentPosition(): int
    {
        $position = $this->file->ftell();

        if ($position === false) {
            throw new ReadException("Unable to get the current position in the file");
        }

        return $position;
    }


    /**
     * @inheritdoc
     */
    public function readNextCallback(callable $func): void
    {
        while (!$this->eof()) {
            $data = $this->read(self::BUFFER_SIZE);
            $result = $func($data);

            # If the callback has finished reading and isn't interested in the rest then stop here
            if ($result === self::CALLBACK_STOP) {
                break;
            }
        }
    }


    /**
     * @inheritdoc
     */
    public function readPreviousCallback(callable $func): void
    {
        while ($this->getCurrentPosition() > 0) {
            $length = self::BUFFER_SIZE;
            if ($this->getCurrentPosition() < $length) {
                $length = $this->getCurrentPosition();
            }

            # Position back to the start of the chunk we want to read
            $this->seek($length * -1);

            # Read the chunk
            $data = $this->read($length);

            # Position back to the start of the chunk we've just read
            $this->seek($length * -1);

            $func($data);
        }
    }


    /**
     * @inheritdoc
     */
    public function getNextPosition(string $string): ?int
    {
        $stringPosition = null;

        $startingPosition = $this->getCurrentPosition();

        $this->readNextCallback(function (string $data) use (&$stringPosition, $string, $startingPosition): ?int {

            $position = strpos($data, $string);
            if ($position === false) {
                if (!$this->eof()) {
                    $length = strlen($string) - 1;
                    $this->seek($length * -1);
                }
                return null;
            }

            # Calculate the position of the string as an offset of the starting position
            $stringPosition = $this->getCurrentPosition() - $startingPosition - strlen($data) + $position;

            # Tell the readNextCallback() that we're done reading
            return self::CALLBACK_STOP;
        });

        # Position back to where we were before finding the string
        $this->seekFromStart($startingPosition);

        return $stringPosition;
    }


    /**
     * @inheritdoc
     */
    public function getPreviousPosition(string $string): ?int
    {
        $stringPosition = null;

        $startingPosition = $this->getCurrentPosition();

        $this->readPreviousCallback(function ($data) use (&$stringPosition, $string, $startingPosition): ?int {

            $position = strrpos($data, $string);
            if ($position === false) {
                if ($this->getCurrentPosition() > 0) {
                    $length = strlen($string) - 1;
                    $this->seek($length);
                }
                return null;
            }

            # Calculate the position of the string as an offset of the starting position
            $stringPosition = $this->getCurrentPosition() - $startingPosition + $position;

            # Tell the readNextCallback() that we're done reading
            return self::CALLBACK_STOP;
        });

        # Position back to where we were before finding the string
        $this->seekFromStart($startingPosition);

        return $stringPosition;
    }
}
