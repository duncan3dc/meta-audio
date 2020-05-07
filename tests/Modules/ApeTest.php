<?php

namespace duncan3dc\MetaAudioTests\Modules;

use duncan3dc\MetaAudio\Exceptions\BadMethodCallException;
use duncan3dc\MetaAudio\File;
use duncan3dc\MetaAudio\Modules\Ape;
use duncan3dc\ObjectIntruder\Intruder;
use PHPUnit\Framework\TestCase;

use function assertFileEquals;

class ApeTest extends TestCase
{

    public function testParseHeader()
    {
        $module = new Intruder(new Ape());

        $file = new File("php://memory");
        $file->write("NOPE");
        $file->rewind();
        $module->open($file);

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage("Invalid Ape tag, expected [APETAGEX], got [NOPE]");
        $module->parseHeader();
    }


    private function getModule($path = null)
    {
        if ($path === null) {
            $path = __DIR__ . "/../data/test.mp3";
        }
        $file = new File($path);

        $module = new Ape();
        $module->open($file);

        return $module;
    }


    public function testGetTitle()
    {
        assertSame("copper colored quiet", $this->getModule()->getTitle());
    }


    public function testGetTrackNumber()
    {
        assertSame(11, $this->getModule()->getTrackNumber());
    }


    public function testGetArtist()
    {
        assertSame("letlive", $this->getModule()->getArtist());
    }


    public function testGetAlbum()
    {
        assertSame("if i'm the devil", $this->getModule()->getAlbum());
    }


    public function testGetYear()
    {
        assertSame(2016, $this->getModule()->getYear());
    }


    /**
     * Ensure we can write all the various tags we support.
     */
    public function testSave1(): void
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

        assertFileEquals(__DIR__ . "/../data/ape-only.mp3", $tmp);
        unlink($tmp);
    }
}
