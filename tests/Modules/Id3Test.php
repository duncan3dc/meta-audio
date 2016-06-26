<?php

namespace duncan3dc\MetaAudioTests\Modules;

use duncan3dc\MetaAudio\File;
use duncan3dc\MetaAudio\Modules\Id3;

class Id3Test extends \PHPUnit_Framework_TestCase
{
    private $module;

    public function setUp()
    {
        $file = new File(__DIR__ . "/../data/test.mp3");
        $this->module = new Id3;
        $this->module->open($file);
    }


    public function testGetTitle()
    {
        $this->assertSame("copper colored quiet", $this->module->getTitle());
    }


    public function testGetTrackNumber()
    {
        $this->assertSame(11, $this->module->getTrackNumber());
    }


    public function testGetArtist()
    {
        $this->assertSame("letlive", $this->module->getArtist());
    }


    public function testGetAlbum()
    {
        $this->assertSame("if i'm the devil", $this->module->getAlbum());
    }


    public function testGetYear()
    {
        $this->assertSame(2016, $this->module->getYear());
    }
}
