<?php

namespace duncan3dc\MetaAudio\Modules;

/**
 * Handle ID3v1.1 tags.
 */
class Id3v1 extends AbstractModule
{
    private const PREAMBLE = "TAG";

    /**
     * Get all the tags from the currently loaded file.
     *
     * @return array
     */
    protected function getTags()
    {
        $this->file->seekFromEnd(-128);

        if ($this->file->read(3) !== self::PREAMBLE) {
            return [];
        }

        $tags = [
            "title"     =>  $this->file->read(30),
            "artist"    =>  $this->file->read(30),
            "album"     =>  $this->file->read(30),
            "year"      =>  $this->file->read(4),
        ];

        foreach ($tags as &$value) {
            $value = rtrim($value, " \0");
        }
        unset($value);

        $track = 0;
        $comment = $this->file->read(30);
        if (substr($comment, 28, 1) === "\0" && substr($comment, 29, 1) !== "\0") {
            $track = ord(substr($comment, 29, 1));
        }
        $tags["track"] = $track;

        return $tags;
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
        $this->file->rewind();

        $contents = $this->file->readAll();

        if (substr($contents, -128, 3) === self::PREAMBLE) {
            $contents = substr($contents, -128);
        }

        $details = $this->createTagData($tags);

        $this->file->truncate();
        $this->file->rewind();
        $this->file->write($contents);
        $this->file->write($details);
    }


    /**
     * Create the tag content for the file.
     *
     * @param array $tags The key/value tags to use
     *
     * @return string
     */
    private function createTagData(array $tags)
    {
        $content = self::PREAMBLE;

        $keys = ["title", "artist", "album", "year", "track"];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $tags)) {
                $tags[$key] = "";
            }
        }

        # Write the standard artist, album, title, etc
        $content .= $this->formatValue($tags["title"], 30);
        $content .= $this->formatValue($tags["artist"], 30);
        $content .= $this->formatValue($tags["album"], 30);
        $content .= $this->formatValue($tags["year"], 4);

        # Don't bother with the 'comment' field
        $content .= $this->formatValue("", 29);

        # Write the track number (technically in the end of the comments field)
        $content .= chr((int) $tags["track"]);

        # We don't support the genre field yet
        $content .= $this->formatValue("", 1);

        if (strlen($content) !== 128) {
            throw new \LengthException("Unable to generate the id3v1.1 tags");
        }

        return $content;
    }


    /**
     * Pad out a value with zero bytes.
     *
     * @param string $value The value to pad
     * @param int $length The length to pad it to
     *
     * @return string
     */
    private function formatValue($value, $length)
    {
        if (strlen($value) > $length) {
            return substr($value, 0, $length);
        }

        return str_pad($value, $length, "\0");
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
        return (int) $this->getTag("track");
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
        return $this->setTag("track", $track);
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
