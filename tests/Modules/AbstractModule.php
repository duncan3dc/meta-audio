<?php

namespace duncan3dc\MetaAudioTests\Modules;

class AbstractModule extends \duncan3dc\MetaAudio\Modules\AbstractModule
{
    private $testTags;

    public function setTags(array $tags)
    {
        $this->testTags = $tags;
    }


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
}
