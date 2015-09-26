<?php

namespace duncan3dc\MetaAudio;

/**
 * A custom file handler.
 */
class File extends \SplFileObject
{
    const CALLBACK_STOP = 408;

    const BUFFER_SIZE = 32768;

    /**
     * Create a new file object.
     *
     * @param string $filename The filename to open
     */
    public function __construct($filename)
    {
        parent::__construct($filename, "r+");
    }


    /**
     * Process the remainder of the file from the current position through the callback.
     *
     * @param callable $func A function that takes a single string parameter (which will contain each chunk of the file read)
     *
     * @return void
     */
    public function readNextCallback(callable $func)
    {
        while (!$this->eof()) {
            $data = $this->fread(self::BUFFER_SIZE);

            if ($data === false) {
                throw new Exception("Failed to read from the file");
            }

            $result = $func($data);

            # If the callback has finished reading and isn't interested in the rest then stop here
            if ($result === self::CALLBACK_STOP) {
                break;
            }
        }
    }


    /**
     * Process the previous contents of the file from the current position through the callback.
     *
     * @param callable $func A function that takes a single string parameter (which will contain each chunk of the file read in reverse)
     *
     * @return void
     */
    public function readPreviousCallback(callable $func)
    {
        while ($this->ftell() > 0) {
            $length = self::BUFFER_SIZE;
            if ($this->ftell() < $length) {
                $length = $this->ftell();
            }

            # Position back to the start of the chunk we want to read
            $this->fseek($length * -1, \SEEK_CUR);

            # Read the chunk
            $data = $this->fread($length);

            # Position back to the start of the chunk we've just read
            $this->fseek($length * -1, \SEEK_CUR);

            if ($data === false) {
                throw new Exception("Failed to read from the file");
            }

            $func($data);
        }
    }


    /**
     * Get the position of the next occurance of a string from the current position.
     *
     * @param string $string The string to search for
     *
     * @return int|false Either the position of the string or false if it doesn't exist
     */
    public function getNextPosition($string)
    {
        $stringPosition = false;

        $startingPosition = $this->ftell();

        $this->readNextCallback(function ($data) use (&$stringPosition, $string, $startingPosition) {

            $position = strpos($data, $string);
            if ($position === false) {
                if (!$this->eof()) {
                    $length = strlen($string) - 1;
                    $this->fseek($length * -1, \SEEK_CUR);
                }
                return;
            }

            # Calculate the position of the string as an offset of the starting position
            $stringPosition = $this->ftell() - $startingPosition - strlen($data) + $position;

            # Tell the readNextCallback() that we're done reading
            return self::CALLBACK_STOP;
        });

        # Position back to where we were before finding the string
        $this->fseek($startingPosition, \SEEK_SET);

        return $stringPosition;
    }


    /**
     * Get the position of the previous occurance of a string from the current position.
     *
     * @param string $string The string to search for
     *
     * @return int|false Either the position of the string or false if it doesn't exist
     */
    public function getPreviousPosition($string)
    {
        $stringPosition = false;

        $startingPosition = $this->ftell();

        $this->readPreviousCallback(function ($data) use (&$stringPosition, $string, $startingPosition) {

            $position = strrpos($data, $string);
            if ($position === false) {
                if ($this->ftell() > 0) {
                    $length = strlen($string) - 1;
                    $this->fseek($length, \SEEK_CUR);
                }
                return;
            }

            # Calculate the position of the string as an offset of the starting position
            $stringPosition = $this->ftell() - $startingPosition + $position;

            # Tell the readNextCallback() that we're done reading
            return self::CALLBACK_STOP;
        });

        # Position back to where we were before finding the string
        $this->fseek($startingPosition, \SEEK_SET);

        return $stringPosition;
    }


    /**
     * Get the rest of the file's contents from the current position.
     *
     * @return string
     */
    public function readAll()
    {
        $contents = "";

        $this->readNextCallback(function($data) use(&$contents) {
            $contents .= $data;
        });

        return $contents;
    }
}
