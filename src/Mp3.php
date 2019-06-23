<?php

namespace duncan3dc\MetaAudio;

use duncan3dc\MetaAudio\Interfaces\FileInterface;

/**
 * Read/write tags from an mp3 file.
 */
class Mp3 extends ReadOnly\Mp3 implements ModuleManagerInterface
{
    use ModuleManager;

    /**
     * @var FileInterface $file The file handler.
     */
    private $file;


    /**
     * Create a new instance from a local file.
     *
     * @param FileInterface $file The file to work with
     */
    public function __construct(FileInterface $file)
    {
        parent::__construct($file);
        $this->file = $file;
    }


    /**
     * Set a value using all active modules.
     *
     * @param string $method The method name to call on the modules
     * @param mixed $value The value to pass to the module method
     *
     * @return $this
     */
    private function setModuleValue($method, $value)
    {
        foreach ($this->modules as $module) {
            $module->open($this->file);
            $module->$method($value);
        }

        return $this;
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
        return $this->setModuleValue(__FUNCTION__, (string) $title);
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
        return $this->setModuleValue(__FUNCTION__, (int) $track);
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
        return $this->setModuleValue(__FUNCTION__, (string) $artist);
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
        return $this->setModuleValue(__FUNCTION__, (string) $album);
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
        return $this->setModuleValue(__FUNCTION__, (int) $year);
    }
}
