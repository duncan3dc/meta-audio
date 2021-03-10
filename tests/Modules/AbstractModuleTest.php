<?php

namespace duncan3dc\MetaAudioTests\Modules;

use duncan3dc\MetaAudio\File;
use duncan3dc\ObjectIntruder\Intruder;
use PHPUnit\Framework\TestCase;

use function assertSame;

class AbstractModuleTest extends TestCase
{
    private $module;

    protected function setUp(): void
    {
        $module = new AbstractModule();
        $module->putTags([
            "artist"    =>  "lagwagon",
        ]);

        $this->module = new Intruder($module);
    }


    protected function tearDown(): void
    {
        unset($this->module);
    }


    public function testOpen()
    {
        # Ensure the default tags are being used
        assertSame("lagwagon", $this->module->getArtist());

        # Ensure the cached data is being used
        $this->module->tags = [
            "artist"    =>  "no use for a name",
        ];
        assertSame("no use for a name", $this->module->getArtist());

        # When passing in a new file, ensure the cached data is discarded
        $tmp = tempnam(sys_get_temp_dir(), "phpunit");
        $file = new File($tmp);
        $this->module->open($file);
        assertSame("lagwagon", $this->module->getArtist());
        unlink($tmp);
    }


    public function testOpenDifferentFile()
    {
        $tmp1 = tempnam(sys_get_temp_dir(), "phpunit");
        $this->module->open(new File($tmp1));
        assertSame("lagwagon", $this->module->getArtist());

        # Ensure the cached data is being used
        $this->module->tags = [
            "artist"    =>  "no use for a name",
        ];
        assertSame("no use for a name", $this->module->getArtist());

        # Ensure when adding a different file the data is reloaded
        $tmp2 = tempnam(sys_get_temp_dir(), "phpunit");
        $this->module->open(new File($tmp2));
        assertSame("lagwagon", $this->module->getArtist());

        unlink($tmp1);
        unlink($tmp2);
    }


    public function testOpenSameFile()
    {
        $tmp = tempnam(sys_get_temp_dir(), "phpunit");

        $this->module->open(new File($tmp));
        assertSame("lagwagon", $this->module->getArtist());

        # Ensure the cached data is being used
        $this->module->tags = [
            "artist"    =>  "no use for a name",
        ];
        assertSame("no use for a name", $this->module->getArtist());

        # Ensure when adding the same file the cache is retained
        $this->module->open(new File($tmp));
        assertSame("no use for a name", $this->module->getArtist());

        unlink($tmp);
    }


    public function testInvalidTag()
    {
        assertSame("", $this->module->getTitle());
    }


    /**
     * Ensure that calling save writes pending changes to the file.
     */
    public function testSave1(): void
    {
        $this->module->setTag("artist", "strung out");

        # Nothing should be written to the file yet
        assertSame(["artist" => "lagwagon"], $this->module->testTags);

        $this->module->save();

        # Now the file should have been written to
        assertSame(["artist" => "strung out"], $this->module->testTags);
    }
}
