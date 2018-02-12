<?php

namespace duncan3dc\MetaAudio\Modules;

use duncan3dc\MetaAudio\Exceptions\ApeParseException;
use duncan3dc\MetaAudio\Exceptions\BadMethodCallException;
use duncan3dc\MetaAudio\Utils\Bit;

/**
 * Handle APE tags.
 */
class Ape extends AbstractModule
{
    const PREAMBLE = "APETAGEX";

    /**
     * Get all the tags from the currently loaded file.
     *
     * @return array
     */
    protected function getTags()
    {
        $this->file->fseek(0, \SEEK_END);

        # Loop until we find a valid set of tags.
        while (true) {
            $position = $this->file->getPreviousPosition(self::PREAMBLE);

            # It looks like there aren't any parsable ape tags in the file
            if ($position === false) {
                break;
            }

            # Convert the start from a relative position to a literal
            $position += $this->file->ftell();

            $this->file->fseek($position, \SEEK_SET);

            try {
                return $this->parseTags();
            } catch (ApeParseException $e) {
                # Ensure we position back to before these tags so we don't pick them up again
                $this->file->fseek($position, \SEEK_SET);
                continue;
            }
        }

        return [];
    }


    /**
     * Parse the tags.
     *
     * @return array
     */
    private function parseTags()
    {
        $header = $this->parseHeader();

        if ($header["footer"]) {
            $this->file->fseek($header["size"] * -1, \SEEK_CUR);
        }

        $tags = [];
        for ($i = 0; $i < $header["items"]; $i++) {
            list($key, $value) = $this->parseItem();
            $tags[strtolower($key)] = $value;
        }

        return $tags;
    }


    /**
     * Parse the header from the file.
     *
     * @return array
     */
    private function parseHeader()
    {
        $preamble = $this->file->fread(8);
        if ($preamble !== self::PREAMBLE) {
            throw new BadMethodCallException("Invalid Ape tag, expected [" . self::PREAMBLE . "], got [{$preamble}]");
        }

        $version = unpack("L", $this->read(4))[1];
        $size = unpack("L", $this->read(4))[1];
        $items = unpack("L", $this->read(4))[1];
        $flags = unpack("L", $this->read(4))[1];

        $header = [
            "version"   =>  $version,
            "size"      =>  $size,
            "items"     =>  $items,
            "flags"     =>  $flags,
            "footer"    =>  Bit::isOff($flags, 29),
        ];

        # Skip the empty space at the end of the header
        $this->file->fread(8);

        return $header;
    }


    /**
     * Get the next item tag from the file.
     *
     * @return array An array with 2 elements, the first is the item key, the second is the item's value
     */
    private function parseItem()
    {
        $length = unpack("L", $this->read(4))[1];

        $flags = unpack("L", $this->read(4))[1];

        $key = "";
        while (!$this->file->eof()) {
            $char = $this->file->fread(1);
            if ($char === pack("c", 0x00)) {
                break;
            }
            $key .= $char;
        }

        if ($length > 0) {
            $value = $this->read($length);
        } else {
            $value = "";
        }

        return [$key, $value];
    }


    /**
     * Read some bytes from the file.
     *
     * @param int $bytes The number of bytes to read
     *
     * @return string
     */
    private function read($bytes)
    {
        $string = "";

        # Read in chunks so an invalid size doesn't cause excessive memory usage
        $remaining = $bytes;
        while ($remaining > 0) {
            if ($this->file->eof()) {
                throw new ApeParseException("Unexpected end of file");
            }

            $size = 1024;
            if ($size > $remaining) {
                $size = $remaining;
            }
            $string .= $this->file->fread($size);
            $remaining -= $size;
        }

        if (strlen($string) !== $bytes) {
            throw new ApeParseException("Unexpected end of file");
        }

        return $string;
    }


    /**
     * Write the specified tags to the currently loaded file.
     *
     * @param array The tags to write as key/value pairs
     *
     * @return void
     */
    protected function putTags(array $tags)
    {
        # Get the contents of the file (without the ape tags)
        $contents = "";
        $this->file->rewind();
        while (true) {
            $start = $this->file->getNextPosition(self::PREAMBLE);
            if ($start === false) {
                break;
            }

            /**
             * Remember where we currently are in the file,
             * as we're about to start moving around.
             */
            $current = $this->file->ftell();

            # Convert the start from a relative position to a literal
            $start = $current + $start;

            # Position to the ape tag and read in the header
            $this->file->fseek($start, \SEEK_SET);
            $header = $this->parseHeader();

            # If this is a footer then find the tag's actual start position
            if ($header["footer"]) {
                $start -= $header["size"];
                $end = $this->file->ftell();
            } else {
                $end = $current + $header["size"];
            }

            # Jump back to where we last read to
            $this->file->fseek($current, \SEEK_SET);

            # Get any content before the ape tag
            if ($start > $current) {
                $contents .= $this->file->fread($start - $current);
            }

            # Seek passed the ape tag we found
            $this->file->fseek($end, \SEEK_SET);
        }

        # Read the rest of the file (following the last ape tag)
        $contents .= $this->file->readAll();

        # Generate the new ape tags
        $tags = $this->createTagData($tags);

        # Empty the file and position at the start so we can overwrite
        $this->file->ftruncate(0);
        $this->file->rewind();

        $this->file->fwrite($contents);
        $this->file->fwrite($tags);
    }


    /**
     * Create the header for the file.
     *
     * @param array The tags to write as key/value pairs
     *
     * @return string
     */
    private function createTagData(array $tags)
    {
        $items = "";
        foreach ($tags as $key => $value) {
            $items .= pack("L", strlen($value));
            $items .= pack("L", 0);
            $items .= $key;
            $items .= pack("c", 0x00);
            $items .= $value;
        }

        $footer = self::PREAMBLE;

        # Version
        $footer .= pack("L", 2000);

        # Size (including the bytes for the footer)
        $footer .= pack("L", strlen($items) + 32);

        # Number of tags
        $footer .= pack("L", count($tags));

        # Flags
        $footer .= pack("L", 0);

        $footer .= str_repeat(" ", 8);

        return $items . $footer;
    }


    /**
     * Get the track title.
     *
     * @return string
     */
    public function getTitle()
    {
        return (string) $this->getTag("title");
    }


    /**
     * Get the track number.
     *
     * @return int
     */
    public function getTrackNumber()
    {
        return (int) $this->getTag("tracknumber");
    }


    /**
     * Get the artist name.
     *
     * @return string
     */
    public function getArtist()
    {
        return (string) $this->getTag("artist");
    }


    /**
     * Get the album name.
     *
     * @return string
     */
    public function getAlbum()
    {
        return (string) $this->getTag("album");
    }


    /**
     * Get the release year.
     *
     * @return int
     */
    public function getYear()
    {
        return (int) $this->getTag("year");
    }


    /**
     * Set the track title.
     *
     * @param string $title The title name
     *
     * @return $this
     */
    public function setTitle($title)
    {
        return $this->setTag("title", $title);
    }


    /**
     * Set the track number.
     *
     * @param int $track The track number
     *
     * @return $this
     */
    public function setTrackNumber($track)
    {
        return $this->setTag("tracknumber", $track);
    }


    /**
     * Set the artist name.
     *
     * @param string $artist The artist name
     *
     * @return $this
     */
    public function setArtist($artist)
    {
        return $this->setTag("artist", $artist);
    }


    /**
     * Set the album name.
     *
     * @param string $album The album name
     *
     * @return $this
     */
    public function setAlbum($album)
    {
        return $this->setTag("album", $album);
    }


    /**
     * Set the release year.
     *
     * @param int $year The release year
     *
     * @return $this
     */
    public function setYear($year)
    {
        return $this->setTag("year", $year);
    }
}
