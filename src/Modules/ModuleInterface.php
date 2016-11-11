<?php

namespace duncan3dc\MetaAudio\Modules;

use duncan3dc\MetaAudio\Interfaces\FileInterface;

/**
 * Interface that all modules must implement to read/write tags.
 */
interface ModuleInterface
{

    /**
     * Load the passed file.
     *
     * @param FileInterface $file The file to read
     *
     * @return $this
     */
    public function open(FileInterface $file);


    /**
     * Save the changes currently pending.
     *
     * @return $this
     */
    public function save();


    /**
     * Throw away any pending changes.
     *
     * @return $this
     */
    public function revert();


    /**
     * Get the track title.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Get the track number.
     *
     * @return int
     */
    public function getTrackNumber();

    /**
     * Get the artist name.
     *
     * @return string
     */
    public function getArtist();

    /**
     * Get the album name.
     *
     * @return string
     */
    public function getAlbum();

    /**
     * Get the release year.
     *
     * @return int
     */
    public function getYear();

    /**
     * Set the track title.
     *
     * @param string $title The title name
     *
     * @return $this
     */
    public function setTitle($title);

    /**
     * Set the track number.
     *
     * @param int $track The track number
     *
     * @return $this
     */
    public function setTrackNumber($track);

    /**
     * Set the artist name.
     *
     * @param string $artist The artist name
     *
     * @return $this
     */
    public function setArtist($artist);

    /**
     * Set the album name.
     *
     * @param string $album The album name
     *
     * @return $this
     */
    public function setAlbum($album);

    /**
     * Set the release year.
     *
     * @param int $year The release year
     *
     * @return $this
     */
    public function setYear($year);
}
