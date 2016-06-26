<?php

namespace duncan3dc\MetaAudioTests\Modules;

use duncan3dc\MetaAudio\File;

class AbstractModuleTest extends \PHPUnit_Framework_TestCase
{
    private $module;

    public function setUp()
    {
        $this->module = new AbstractModule;
        $this->module->setTags([
            "artist"    =>  "lagwagon",
        ]);
    }


    private function setCachedTags(array $tags)
    {
        $reflected = new \ReflectionClass($this->module);
        $property = $reflected->getProperty("tags");
        $property->setAccessible(true);
        $property->setValue($this->module, $tags);
    }


    public function testOpen()
    {
        # Ensure the default tags are being used
        $this->assertSame("lagwagon", $this->module->getArtist());

        # Ensure the cached data is being used
        $this->setCachedTags([
            "artist"    =>  "no use for a name",
        ]);
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
        $this->setCachedTags([
            "artist"    =>  "no use for a name",
        ]);
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
        $this->setCachedTags([
            "artist"    =>  "no use for a name",
        ]);
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
}
