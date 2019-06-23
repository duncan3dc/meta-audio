<?php

namespace duncan3dc\MetaAudioTests;

use duncan3dc\MetaAudio\File;
use duncan3dc\MetaAudio\Modules\Ape;
use duncan3dc\MetaAudio\Modules\Id3v1;
use duncan3dc\MetaAudio\Modules\Id3v2;
use duncan3dc\MetaAudio\Modules\ModuleInterface;
use duncan3dc\MetaAudio\Mp3;
use PHPUnit\Framework\TestCase;
use function assertSame;

class ConvertTest extends TestCase
{

    private function rewriteOldFileWithNewTags(ModuleInterface $module): void
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
        assertSame($title, $mp3->getTitle());
        assertSame($track, $mp3->getTrackNumber());
        assertSame($artist, $mp3->getArtist());
        assertSame($album, $mp3->getAlbum());
        assertSame($year, $mp3->getYear());

        # Create a new instance to rule out any caching
        unset($mp3);
        $mp3 = new Mp3($file);
        $mp3->addModule($module);

        # Ensure the tags we parse from our newly written file are still the same
        assertSame($title, $mp3->getTitle());
        assertSame($track, $mp3->getTrackNumber());
        assertSame($artist, $mp3->getArtist());
        assertSame($album, $mp3->getAlbum());
        assertSame($year, $mp3->getYear());

        unlink($tmp);
    }


    /**
     * Ensure we can rewrite ape tags.
     */
    public function testApe(): void
    {
        $this->rewriteOldFileWithNewTags(new Ape());
    }


    /**
     * Ensure we can rewrite id3v1 tags.
     */
    public function testId3v1(): void
    {
        $this->rewriteOldFileWithNewTags(new Id3v1());
    }


    /**
     * Ensure we can rewrite id3v2 tags.
     */
    public function testId3v2(): void
    {
        $this->rewriteOldFileWithNewTags(new Id3v2());
    }
}
