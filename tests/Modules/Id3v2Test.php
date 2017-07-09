<?php

namespace duncan3dc\MetaAudioTests\Modules;

use duncan3dc\MetaAudio\File;
use duncan3dc\MetaAudio\Modules\Id3v2;
use duncan3dc\ObjectIntruder\Intruder;

class Id3v2Test extends \PHPUnit_Framework_TestCase
{
    private $module;

    public function setUp()
    {
        $file = new File(__DIR__ . "/../data/test.mp3");
        $this->module = new Id3v2;
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


    public function synchsafeProvider()
    {
        $numbers = [0, 1, 9999];
        for ($i = 0; $i <= 99; $i++) {
            $numbers[] = rand(2, 9998);
        }
        foreach ($numbers as $number) {
            yield [$number];
        }
    }
    /**
     * @dataProvider synchsafeProvider
     */
    public function testSynchsafeConversion($input)
    {
        $module = new Intruder($this->module);

        $string = $module->toSynchsafeInt($input);
        $result = $module->fromSynchsafeInt($string);

        $this->assertSame($input, $result);
    }
}
