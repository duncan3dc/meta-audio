<?php

namespace duncan3dc\MetaAudioTests\Modules;

use duncan3dc\MetaAudio\File;
use duncan3dc\MetaAudio\Modules\Id3v2;
use duncan3dc\ObjectIntruder\Intruder;
use PHPUnit\Framework\TestCase;

use function assertSame;

class Id3v2Test extends TestCase
{
    private $module;

    protected function setUp(): void
    {
        $file = new File(__DIR__ . "/../data/test.mp3");
        $this->module = new Id3v2();
        $this->module->open($file);
    }


    public function testGetTitle()
    {
        assertSame("copper colored quiet", $this->module->getTitle());
    }


    public function testGetTrackNumber()
    {
        assertSame(11, $this->module->getTrackNumber());
    }


    public function testGetArtist()
    {
        assertSame("letlive", $this->module->getArtist());
    }


    public function testGetAlbum()
    {
        assertSame("if i'm the devil", $this->module->getAlbum());
    }


    public function testGetYear()
    {
        assertSame(2016, $this->module->getYear());
    }


    private function getEncoded()
    {
        $file = new File(__DIR__ . "/../data/id3v2-encoding.mp3");

        $module = new Id3v2();
        $module->open($file);

        return $module;
    }
    public function testIso88591()
    {
        $module = $this->getEncoded();
        assertSame("Eidola", $module->getArtist());
    }
    public function testUtf16WithBom()
    {
        $module = $this->getEncoded();
        assertSame("To Speak, to Listen", $module->getAlbum());
    }
    public function testUtf16WithoutBom()
    {
        $module = $this->getEncoded();
        assertSame("The Abstract of a Planet in Resolve", $module->getTitle());
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

        assertSame($input, $result);
    }
}
