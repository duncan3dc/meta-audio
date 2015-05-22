<?php

namespace duncan3dc\MetaAudio\Modules;

use duncan3dc\MetaAudio\Exception;

/**
 * Handle APE tags.
 */
class Ape extends AbstractModule
{

    /**
     * Get all the tags from the currently loaded file.
     *
     * @return array
     */
    protected function getTags()
    {
        $this->file->fseek(0, \SEEK_SET);
        $position = $this->file->getStringPosition("APETAGEX");
        $this->file->fseek($position);

        $header = $this->parseHeader();

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
    protected function parseHeader()
    {
        $preamble = $this->file->fread(8);
        if ($preamble !== "APETAGEX") {
            throw new Exception("Invalid Ape tag, expected [APETAGEX], got [{$preamble}]");
        }

        $header = [
            "version"   =>  unpack("L", $this->file->fread(4))[1],
            "size"      =>  unpack("L", $this->file->fread(4))[1],
            "items"     =>  unpack("L", $this->file->fread(4))[1],
            "flags"     =>  unpack("L", $this->file->fread(4))[1],
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
    protected function parseItem()
    {
        $length = unpack("L", $this->file->fread(4))[1];

        $flags = unpack("L", $this->file->fread(4))[1];

        $key = "";
        while (!$this->file->eof()) {
            $char = $this->file->fread(1);
            if ($char === pack("c", 0x00)) {
                break;
            }
            $key .= $char;
        }

        if ($length > 0) {
            $value = $this->file->fread($length);
        } else {
            $value = "";
        }

        return [$key, $value];
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
}
