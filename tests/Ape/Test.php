<?php

namespace duncan3dc\MetaAudioTests\Ape;

use duncan3dc\MetaAudio\File;
use duncan3dc\MetaAudio\Modules\Ape;

class Test extends \PHPUnit_Framework_TestCase
{
    public function getFile($name)
    {
        return new File(__DIR__ . "/files/{$name}.mp3");
    }


    public function getModule($name)
    {
        $file = $this->getFile($name);

        $module = new Ape;
        $module->open($file);

        return $module;
    }


    public function test_can_handle_extra_corrupt_tags_at_the_end()
    {
        $module = $this->getModule("extra_corrupt_tags_at_end");

        $this->assertSame("protest the hero", $module->getArtist());
        $this->assertSame("pacific myth", $module->getAlbum());
        $this->assertSame(2015, $module->getYear());
        $this->assertSame(3, $module->getTrackNumber());
        $this->assertSame("cold water", $module->getTitle());
    }
}
