<?php

namespace duncan3dc\MetaAudioTests\Modules;

use duncan3dc\MetaAudio\File;
use duncan3dc\ObjectIntruder\Intruder;

class AbstractModuleTest extends \PHPUnit_Framework_TestCase
{
    private $module;

    public function setUp()
    {
        $module = new AbstractModule;
        $module->putTags([
            "artist"    =>  "lagwagon",
        ]);

        $this->module = new Intruder($module);
    }


    public function tearDown()
    {
        unset($this->module);
    }


    public function testOpen()
    {
        # Ensure the default tags are being used
        $this->assertSame("lagwagon", $this->module->getArtist());

        # Ensure the cached data is being used
        $this->module->tags = [
            "artist"    =>  "no use for a name",
        ];
        $this->assertSame("no use for a name", $this->module->getArtist());

        # When passing in a new file, ensure the cached data is discarded
        $tmp = tempnam(sys_get_temp_dir(), "phpunit");
        $file = new File($tmp);
        $this->module->open($file);
        $this->assertSame("lagwagon", $this->module->getArtist());
        unlink($tmp);
    }


    public function testOpenDifferentFile()
    {
        $tmp1 = tempnam(sys_get_temp_dir(), "phpunit");
        $this->module->open(new File($tmp1));
        $this->assertSame("lagwagon", $this->module->getArtist());

        # Ensure the cached data is being used
        $this->module->tags = [
            "artist"    =>  "no use for a name",
        ];
        $this->assertSame("no use for a name", $this->module->getArtist());

        # Ensure when adding a different file the data is reloaded
        $tmp2 = tempnam(sys_get_temp_dir(), "phpunit");
        $this->module->open(new File($tmp2));
        $this->assertSame("lagwagon", $this->module->getArtist());

        unlink($tmp1);
        unlink($tmp2);
    }


    public function testOpenSameFile()
    {
        $tmp = tempnam(sys_get_temp_dir(), "phpunit");

        $this->module->open(new File($tmp));
        $this->assertSame("lagwagon", $this->module->getArtist());

        # Ensure the cached data is being used
        $this->module->tags = [
            "artist"    =>  "no use for a name",
        ];
        $this->assertSame("no use for a name", $this->module->getArtist());

        # Ensure when adding the same file the cache is retained
        $this->module->open(new File($tmp));
        $this->assertSame("no use for a name", $this->module->getArtist());

        unlink($tmp);
    }


    public function testInvalidTag()
    {
        $this->assertSame("", $this->module->getTitle());
    }


    public function test_save_writes_pending_changes()
    {
        $this->module->setTag("artist", "strung out");

        # Nothing should be written to the file yet
        $this->assertSame(["artist" => "lagwagon"], $this->module->testTags);

        $this->module->save();

        # Now the file should have been written to
        $this->assertSame(["artist" => "strung out"], $this->module->testTags);

        # Overwrite the cache data, so that we can be sure the file ISN'T written to again
        $this->module->tags = [
            "artist"    =>  "no use for a name",
        ];

        # The file should not have been written to again
        $this->module->save();
        $this->assertSame(["artist" => "strung out"], $this->module->testTags);
    }
}
