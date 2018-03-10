<?php

namespace duncan3dc\MetaAudioTests\Modules;

use duncan3dc\MetaAudio\Exceptions\BadMethodCallException;
use duncan3dc\MetaAudio\File;
use duncan3dc\MetaAudio\Modules\Ape;
use duncan3dc\ObjectIntruder\Intruder;

class ApeTest extends \PHPUnit_Framework_TestCase
{

    public function testParseHeader()
    {
        $module = new Intruder(new Ape);

        $file = new File("php://memory");
        $file->fwrite("NOPE");
        $file->rewind();
        $module->open($file);

        $this->setExpectedException(BadMethodCallException::class, "Invalid Ape tag, expected [APETAGEX], got [NOPE]");
        $module->parseHeader();
    }


    private function getModule($path = null)
    {
        if ($path === null) {
            $path = __DIR__ . "/../data/test.mp3";
        }
        $file = new File($path);

        $module = new Ape;
        $module->open($file);

        return $module;
    }


    public function testGetTitle()
    {
        $this->assertSame("copper colored quiet", $this->getModule()->getTitle());
    }


    public function testGetTrackNumber()
    {
        $this->assertSame(11, $this->getModule()->getTrackNumber());
    }


    public function testGetArtist()
    {
        $this->assertSame("letlive", $this->getModule()->getArtist());
    }


    public function testGetAlbum()
    {
        $this->assertSame("if i'm the devil", $this->getModule()->getAlbum());
    }


    public function testGetYear()
    {
        $this->assertSame(2016, $this->getModule()->getYear());
    }


    public function test_can_write_all_tags()
    {
        $tmp = tempnam(sys_get_temp_dir(), "meta-audio-ape-");
        copy(__DIR__ . "/../data/no-tags.mp3", $tmp);

        $module = $this->getModule($tmp);

        $module
            ->setArtist("architects")
            ->setAlbum("lost forever // lost together")
            ->setTitle("naysayer")
            ->setTrackNumber(2)
            ->setYear(2014)
            ->save();

        $this->assertEquals(file_get_contents(__DIR__ . "/../data/ape-only.mp3"), file_get_contents($tmp));
        unlink($tmp);
    }
}
