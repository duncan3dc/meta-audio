<?php

namespace duncan3dc\MetaAudioTests\Ape;

use duncan3dc\MetaAudio\File;
use duncan3dc\MetaAudio\Modules\Ape;
use duncan3dc\PhpIni\State as IniState;

class Test extends \PHPUnit_Framework_TestCase
{
    public function getFile($name)
    {
        return new File(__DIR__ . "/files/{$name}.mp3");
    }


    public function getModule($file)
    {
        if (!$file instanceof File) {
            $file = $this->getFile($file);
        }

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


    public function test_can_fix_extra_corrupt_tags_at_the_end()
    {
        $original = $this->getFile("extra_corrupt_tags_at_end");

        $tmp = tempnam(sys_get_temp_dir(), "meta-audio-");
        $file = new File($tmp);
        $file->fwrite($original->readAll());

        $module = $this->getModule($file);

        $module->setArtist("tesseract")->save();
        $file->rewind();
        $this->assertSame(1, substr_count($file->readAll(), "APETAGEX"));
    }


    public function test_can_handle_invalid_item_length()
    {
        $ini = new IniState;

        /**
         * The purpose of this test is to ensure the library doesn't
         * attempt to allocate a load of memory to read an invalid
         * tag. If the ape tag declares its length is something
         * like 999,999,999 then we need to be able to handle it.
         */
        $ini->set("memory_limit", "1M");

        $result = $ini->call(function () {
            $module = $this->getModule("invalid_item_length");
            return $module->getArtist();
        });

        $this->assertSame("closure in moscow", $result);
    }
}
