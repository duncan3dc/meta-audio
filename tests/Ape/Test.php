<?php

namespace duncan3dc\MetaAudioTests\Ape;

use duncan3dc\MetaAudio\File;
use duncan3dc\MetaAudio\Interfaces\FileInterface;
use duncan3dc\MetaAudio\Modules\Ape;
use duncan3dc\PhpIni\State as IniState;
use PHPUnit\Framework\TestCase;

use function assertSame;

class Test extends TestCase
{
    public function getFile($name)
    {
        return new File(__DIR__ . "/files/{$name}.mp3");
    }


    public function getModule($file)
    {
        if (!$file instanceof FileInterface) {
            $file = $this->getFile($file);
        }

        $module = new Ape();
        $module->open($file);

        return $module;
    }


    /**
     * Ensure we can handle extra corrupt tags at the end.
     */
    public function testRead1(): void
    {
        $module = $this->getModule("extra_corrupt_tags_at_end");

        assertSame("protest the hero", $module->getArtist());
        assertSame("pacific myth", $module->getAlbum());
        assertSame(2015, $module->getYear());
        assertSame(3, $module->getTrackNumber());
        assertSame("cold water", $module->getTitle());
    }


    /**
     * Ensure we can fix extra corrupt tags at the end.
     */
    public function testWrite1(): void
    {
        $original = $this->getFile("extra_corrupt_tags_at_end");

        $tmp = tempnam(sys_get_temp_dir(), "meta-audio-");
        $file = new File($tmp);
        $file->write($original->readAll());

        $module = $this->getModule($file);

        $module->setArtist("tesseract")->save();
        $file->rewind();
        assertSame(1, substr_count($file->readAll(), "APETAGEX"));
    }


    /**
     * Ensure we can handle items with an invalid length.
     */
    public function testRead2(): void
    {
        $ini = new IniState();

        /**
         * The purpose of this test is to ensure the library doesn't
         * attempt to allocate a load of memory to read an invalid
         * tag. If the ape tag declares its length is something
         * like 999,999,999 then we need to be able to handle it.
         */
        $ini->set("memory_limit", "8M");

        $result = $ini->call(function () {
            $module = $this->getModule("invalid_item_length");
            return $module->getArtist();
        });

        assertSame("closure in moscow", $result);
    }
}
