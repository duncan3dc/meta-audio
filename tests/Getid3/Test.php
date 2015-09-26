<?php

namespace duncan3dc\MetaAudioTests\GetId3;

use duncan3dc\MetaAudio\File;
use duncan3dc\MetaAudio\Modules\Ape;
use duncan3dc\MetaAudio\Modules\Id3v1;
use duncan3dc\MetaAudio\Modules\Id3v2;
use duncan3dc\MetaAudio\Mp3;

class Test extends \PHPUnit_Framework_TestCase
{

    public function fileProvider()
    {
        $files = glob(__DIR__ . "/*.mp3");
        foreach ($files as $filename) {
            $data = (new \getID3)->analyze($filename);
            $file = new File($filename);

            $mp3 = new Mp3($file);
            $mp3->addModule(new Ape);
            yield ["ape", $mp3, $data["ape"]["comments"]];

            $mp3 = new Mp3($file);
            $mp3->addModule(new Id3v2);
            yield ["id3v2", $mp3, $data["id3v2"]["comments"]];
        }
    }


    /**
     * @dataProvider fileProvider
     */
    public function testGetTitle($type, Mp3 $mp3, array $data)
    {
        $this->assertEquals(@$data["title"][0], $mp3->getTitle());
    }


    /**
     * @dataProvider fileProvider
     */
    public function testGetTrackNumber($type, Mp3 $mp3, array $data)
    {
        $key = isset($data["track"]) ? "track" : "track_number";
        $this->assertEquals(@$data[$key][0], $mp3->getTrackNumber());
    }


    /**
     * @dataProvider fileProvider
     */
    public function testGetArtist($type, Mp3 $mp3, array $data)
    {
        $this->assertEquals(@$data["artist"][0], $mp3->getArtist());
    }


    /**
     * @dataProvider fileProvider
     */
    public function testGetAlbum($type, Mp3 $mp3, array $data)
    {
        $this->assertEquals(@$data["album"][0], $mp3->getAlbum());
    }


    /**
     * @dataProvider fileProvider
     */
    public function testGetYear($type, Mp3 $mp3, array $data)
    {
        $this->assertEquals(@$data["year"][0], $mp3->getYear());
    }
}
