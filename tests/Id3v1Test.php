<?php

namespace duncan3dc\MetaAudioTests;

use duncan3dc\MetaAudio\File;
use duncan3dc\MetaAudio\Modules\Id3;
use duncan3dc\ObjectIntruder\Intruder;

class Id3v1Test extends \PHPUnit_Framework_TestCase
{
    public function getModule()
    {
        $file = new File(__DIR__ . "/data/id3v1.mp3");
        $module = new Id3;
        $module->open($file);
        return $module;
    }


    public function testGetTitle()
    {
        $module = $this->getModule();
        $this->assertSame("intro", $module->getTitle());
    }


    public function testGetTrackNumber()
    {
        $module = $this->getModule();
        $this->assertSame(1, $module->getTrackNumber());
    }


    public function testGetArtist()
    {
        $module = $this->getModule();
        $this->assertSame("the offspring", $module->getArtist());
    }


    public function testGetAlbum()
    {
        $module = $this->getModule();
        $this->assertSame("conspiracy of one", $module->getAlbum());
    }


    public function testGetYear()
    {
        $module = $this->getModule();
        $this->assertSame(2000, $module->getYear());
    }


    private function zeroBytes($length)
    {
        return str_repeat("\0", $length);
    }
    public function simpleTagProvider()
    {
        yield [
            "TAGtitle" . $this->zeroBytes(25) . "artist" . $this->zeroBytes(24) . "album" . $this->zeroBytes(25) . "year" . $this->zeroBytes(29) . "\x05\0",
            ["TIT2" => "title", "TPE1" => "artist", "TALB" => "album", "TDRC" => "year", "TRCK" => 5],
        ];

        # Title field too long
        yield [
            "TAG123456789012345678901234567890artist" . $this->zeroBytes(24) . "album" . $this->zeroBytes(25) . "year" . $this->zeroBytes(29) . "\x05\0",
            ["TIT2" => "123456789012345678901234567890toomany", "TPE1" => "artist", "TALB" => "album", "TDRC" => "year", "TRCK" => 5],
        ];

        # Album field too long
        yield [
            "TAGtitle" . $this->zeroBytes(25) . "123456789012345678901234567890album" . $this->zeroBytes(25) . "year" . $this->zeroBytes(29) . "\x05\0",
            ["TIT2" => "title", "TPE1" => "123456789012345678901234567890toomany", "TALB" => "album", "TDRC" => "year", "TRCK" => 5],
        ];

        # Album field too long
        yield [
            "TAGtitle" . $this->zeroBytes(25) . "artist" . $this->zeroBytes(24) . "123456789012345678901234567890year" . $this->zeroBytes(29) . "\x05\0",
            ["TIT2" => "title", "TPE1" => "artist", "TALB" => "123456789012345678901234567890toomany", "TDRC" => "year", "TRCK" => 5],
        ];

        # Year field too long
        yield [
            "TAGtitle" . $this->zeroBytes(25) . "artist" . $this->zeroBytes(24) . "album" . $this->zeroBytes(25) . "unkn" . $this->zeroBytes(29) . "\x05\0",
            ["TIT2" => "title", "TPE1" => "artist", "TALB" => "album", "TDRC" => "unknown", "TRCK" => 5],
        ];

        # Track field too big
        yield [
            "TAGtitle" . $this->zeroBytes(25) . "artist" . $this->zeroBytes(24) . "album" . $this->zeroBytes(25) . "year" . $this->zeroBytes(29) . "\x9f\0",
            ["TIT2" => "title", "TPE1" => "artist", "TALB" => "album", "TDRC" => "year", "TRCK" => 99999],
        ];
    }
    /**
     * @dataProvider simpleTagProvider
     */
    public function testCreateSimpleTags($expected, array $tags)
    {
        $module = new Intruder(new Id3);

        $result = $module->createSimpleTags($tags);

        $this->assertEquals($expected, $result);
    }
}
