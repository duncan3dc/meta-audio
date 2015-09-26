<?php

namespace duncan3dc\MetaAudio;

/**
 * A custom file handler.
 */
class File extends \SplFileObject
{

    /**
     * Process the remainder of the file from the current position through the callback.
     *
     * @param callable $func A function that takes a single string parameter (which will contain each chunk of the file read)
     *
     * @return void
     */
    public function readCallback(callable $func)
    {
        while (!$this->eof()) {
            $data = $this->fread(8192);

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
    public function getStringPosition($string)
    {
        $stringPosition = false;

        $startingPosition = $this->ftell();

        $this->readCallback(function ($data) use (&$stringPosition, $string, $startingPosition) {

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

        $this->readCallback(function($data) use(&$contents) {
            $contents .= $data;
        });

        return $contents;
    }
}
