<?php

namespace duncan3dc\MetaAudioTests;

use duncan3dc\MetaAudio\Modules\Id3;
use duncan3dc\MetaAudio\Mp3;
use duncan3dc\MetaAudio\Tagger;

class TaggerTest extends \PHPUnit_Framework_TestCase
{

    public function testOpenMp3()
    {
        $tagger = new Tagger;

        $tmp = tempnam("/tmp", "meta-audio-");

        $file = $tagger->open($tmp);

        $this->assertInstanceOf(Mp3::class, $file);

        unlink($tmp);
    }


    public function testOpenModules()
    {
        $tagger = new Tagger;

        $module = new Id3;

        $tagger->addModule($module);

        $tmp = tempnam("/tmp", "meta-audio-");

        $file = $tagger->open($tmp);

        $class = new \ReflectionClass($file);
        $property = $class->getProperty("modules");
        $property->setAccessible(true);
        $modules = $property->getValue($file);

        $this->assertSame($modules, [$module]);

        unlink($tmp);
    }
}
