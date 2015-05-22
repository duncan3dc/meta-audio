<?php

namespace duncan3dc\MetaAudio\Modules;

use duncan3dc\MetaAudio\Exception;

/**
 * Handle ID3 tags.
 */
class Id3 extends AbstractModule
{

    /**
     * Get all the tags from the currently loaded file.
     *
     * @return array
     */
    protected function getTags()
    {
        $this->file->fseek(0, \SEEK_SET);
        $position = $this->file->getStringPosition("ID3");
        $this->file->fseek($position);

        $header = $this->parseHeader();

        $frames = $this->file->fread($header["size"]);

        $tags = [];
        while ($tag = $this->parseItem($frames)) {
            list($key, $value) = $tag;
            $tags[strtoupper($key)] = $value;
        }

        return $tags;
    }


    /**
     * Convert a synchsafe integer to a regular integer.
     *
     * In a synchsafe integer, the most significant bit of each byte is zero,
     * making seven bits out of eight available.
     * So a 32-bit synchsafe integer can only store 28 bits of information.
     *
     * @param string $data The synchsafe integer
     *
     * @param int
     */
    protected function getSynchsafeInt($data)
    {
        $int = 0;
        for ($i = 1; $i <= 4; $i++) {
            $byte = ord(substr($data, $i - 1, 1));
            $int += $byte * pow(2, (4 - $i) * 7);
        }

        return $int;
    }


    /**
     * Parse the header from the file.
     *
     * @return array
     */
    protected function parseHeader()
    {
        $preamble = $this->file->fread(3);
        if ($preamble !== "ID3") {
            throw new Exception("Invalid ID3 tag, expected [ID3], got [{$preamble}]");
        }

        $version = unpack("S", $this->file->fread(2))[1];
        $flags = unpack("C", $this->file->fread(1))[1];

        $header = [
            "version"   =>  $version,
            "flags"     =>  $flags,
            "size"      =>  $this->getSynchsafeInt($this->file->fread(4)),
            "unsynch"   =>  (bool) ($flags & 0x80),
            "footer"    =>  (bool) ($flags & 0x10),
        ];

        # Skip the extended header
        if ($flags & 0x40) {
            $size = $this->getSynchsafeInt($this->file->fread(4));
            $this->file->fread($size - 4);
            $header["size"] -= $size;
        }

        return $header;
    }


    /**
     * Get the next item tag from the file.
     *
     * @param string $frames The frames to parse the next one from
     *
     * @return array An array with 2 elements, the first is the item key, the second is the item's value
     */
    protected function parseItem(&$frames)
    {
        if (strlen($frames) < 1) {
            return;
        }

        $key = substr($frames, 0, 4);

        # Ensure a valid key was found
        if ($key < "AAAA" || $key > "ZZZZ") {
            return;
        }

        $size = $this->getSynchsafeInt(substr($frames, 4, 4));

        $value = substr($frames, 11, $size - 1);

        $frames = substr($frames, 10 + $size);

        return [$key, $value];
    }


    /**
     * Get the track title.
     *
     * @return string
     */
    public function getTitle()
    {
        return (string) $this->getTag("TIT2");
    }


    /**
     * Get the track number.
     *
     * @return int
     */
    public function getTrackNumber()
    {
        return (int) $this->getTag("TRCK");
    }


    /**
     * Get the artist name.
     *
     * @return string
     */
    public function getArtist()
    {
        return (string) $this->getTag("TPE1");
    }


    /**
     * Get the album name.
     *
     * @return string
     */
    public function getAlbum()
    {
        return (string) $this->getTag("TALB");
    }


    /**
     * Get the release year.
     *
     * @return int
     */
    public function getYear()
    {
        return (int) $this->getTag("TDRC");
    }
}
