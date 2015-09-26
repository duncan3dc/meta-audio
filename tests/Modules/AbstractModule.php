<?php

namespace duncan3dc\MetaAudioTests\Modules;

class AbstractModule extends \duncan3dc\MetaAudio\Modules\AbstractModule
{
    private $testTags;


    protected function getTags()
    {
        return $this->testTags;
    }


    public function getTitle()
    {
        return $this->getTag("title");
    }


    public function getTrackNumber()
    {
        return $this->getTag("track-number");
    }


    public function getArtist()
    {
        return $this->getTag("artist");
    }


    public function getAlbum()
    {
        return $this->getTag("album");
    }


    public function getYear()
    {
        return $this->getTag("year");
    }


    public function putTags(array $tags)
    {
        $this->testTags = $tags;
    }


    public function setTitle($title)
    {
        return $this->setTag("title", $title);
    }


    public function setTrackNumber($track)
    {
        return $this->setTag("track-number", $track);
    }


    public function setArtist($artist)
    {
        return $this->setTag("artist", $artist);
    }


    public function setAlbum($album)
    {
        return $this->setTag("album", $album);
    }


    public function setYear($year)
    {
        return $this->setTag("year", $year);
    }
}
