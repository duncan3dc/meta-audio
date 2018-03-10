<?php

namespace duncan3dc\MetaAudioTests;

use duncan3dc\MetaAudio\File;
use duncan3dc\MetaAudio\Modules\Ape;
use duncan3dc\MetaAudio\Modules\Id3v1;
use duncan3dc\MetaAudio\Modules\Id3v2;
use duncan3dc\MetaAudio\Modules\ModuleInterface;
use duncan3dc\MetaAudio\Mp3;

class ConvertTest extends \PHPUnit_Framework_TestCase
{

    private function rewrite_an_old_file_with_new_tags(ModuleInterface $module)
    {
        $tmp = tempnam(sys_get_temp_dir(), "meta-audio-");
        copy(__DIR__ . "/data/old.mp3", $tmp);

        $file = new File($tmp);

        $mp3 = new Mp3($file);
        $mp3->addModule($module);

        # Get all the attributes
        $title = $mp3->getTitle();
        $track = $mp3->getTrackNumber();
        $artist = $mp3->getArtist();
        $album = $mp3->getAlbum();
        $year = $mp3->getYear();

        # Re-set all the attributes
        $mp3->setTitle($title);
        $mp3->setTrackNumber($track);
        $mp3->setArtist($artist);
        $mp3->setAlbum($album);
        $mp3->setYear($year);

        # Ensure the attributes are the same after setting them
        $this->assertSame($title, $mp3->getTitle());
        $this->assertSame($track, $mp3->getTrackNumber());
        $this->assertSame($artist, $mp3->getArtist());
        $this->assertSame($album, $mp3->getAlbum());
        $this->assertSame($year, $mp3->getYear());

        # Create a new instance to rule out any caching
        unset($mp3);
        $mp3 = new Mp3($file);
        $mp3->addModule($module);

        # Ensure the tags we parse from our newly written file are still the same
        $this->assertSame($title, $mp3->getTitle());
        $this->assertSame($track, $mp3->getTrackNumber());
        $this->assertSame($artist, $mp3->getArtist());
        $this->assertSame($album, $mp3->getAlbum());
        $this->assertSame($year, $mp3->getYear());

        unlink($tmp);
    }


    public function test_rewriting_tags_with_ape()
    {
        $this->rewrite_an_old_file_with_new_tags(new Ape);
    }


    public function test_rewriting_tags_with_id3v1()
    {
        $this->rewrite_an_old_file_with_new_tags(new Id3v1);
    }


    public function test_rewriting_tags_with_id3v2()
    {
        $this->rewrite_an_old_file_with_new_tags(new Id3v2);
    }
}
