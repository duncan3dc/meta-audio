<?php

namespace duncan3dc\MetaAudioTests\Modules;

use duncan3dc\MetaAudio\File;
use duncan3dc\MetaAudio\Modules\Id3v1;
use duncan3dc\ObjectIntruder\Intruder;
use PHPUnit\Framework\TestCase;
use function assertEquals;
use function assertSame;

class Id3v1Test extends TestCase
{
    public function getModule()
    {
        $file = new File(__DIR__ . "/../data/id3v1.mp3");
        $module = new Id3v1;
        $module->open($file);
        return $module;
    }


    public function testGetTitle()
    {
        $module = $this->getModule();
        assertSame("intro", $module->getTitle());
    }


    public function testGetTrackNumber()
    {
        $module = $this->getModule();
        assertSame(1, $module->getTrackNumber());
    }


    public function testGetArtist()
    {
        $module = $this->getModule();
        assertSame("the offspring", $module->getArtist());
    }


    public function testGetAlbum()
    {
        $module = $this->getModule();
        assertSame("conspiracy of one", $module->getAlbum());
    }


    public function testGetYear()
    {
        $module = $this->getModule();
        assertSame(2000, $module->getYear());
    }


    private function zeroBytes($length)
    {
        return str_repeat("\0", $length);
    }
    public function tagProvider()
    {
        yield [
            "TAGtitle" . $this->zeroBytes(25) . "artist" . $this->zeroBytes(24) . "album" . $this->zeroBytes(25) . "year" . $this->zeroBytes(29) . "\x05\0",
            ["title" => "title", "artist" => "artist", "album" => "album", "year" => "year", "track" => 5],
        ];

        # Title field too long
        yield [
            "TAG123456789012345678901234567890artist" . $this->zeroBytes(24) . "album" . $this->zeroBytes(25) . "year" . $this->zeroBytes(29) . "\x05\0",
            ["title" => "123456789012345678901234567890toomany", "artist" => "artist", "album" => "album", "year" => "year", "track" => 5],
        ];

        # Album field too long
        yield [
            "TAGtitle" . $this->zeroBytes(25) . "123456789012345678901234567890album" . $this->zeroBytes(25) . "year" . $this->zeroBytes(29) . "\x05\0",
            ["title" => "title", "artist" => "123456789012345678901234567890toomany", "album" => "album", "year" => "year", "track" => 5],
        ];

        # Album field too long
        yield [
            "TAGtitle" . $this->zeroBytes(25) . "artist" . $this->zeroBytes(24) . "123456789012345678901234567890year" . $this->zeroBytes(29) . "\x05\0",
            ["title" => "title", "artist" => "artist", "album" => "123456789012345678901234567890toomany", "year" => "year", "track" => 5],
        ];

        # Year field too long
        yield [
            "TAGtitle" . $this->zeroBytes(25) . "artist" . $this->zeroBytes(24) . "album" . $this->zeroBytes(25) . "unkn" . $this->zeroBytes(29) . "\x05\0",
            ["title" => "title", "artist" => "artist", "album" => "album", "year" => "unknown", "track" => 5],
        ];

        # Track field too big
        yield [
            "TAGtitle" . $this->zeroBytes(25) . "artist" . $this->zeroBytes(24) . "album" . $this->zeroBytes(25) . "year" . $this->zeroBytes(29) . "\x9f\0",
            ["title" => "title", "artist" => "artist", "album" => "album", "year" => "year", "track" => 99999],
        ];
    }
    /**
     * @dataProvider tagProvider
     */
    public function testCreateTagData($expected, array $tags)
    {
        $module = new Intruder(new Id3v1);

        $result = $module->createTagData($tags);

        assertEquals($expected, $result);
    }
}
