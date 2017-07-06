<?php

namespace duncan3dc\MetaAudioTests;

use duncan3dc\MetaAudio\Modules\Id3;
use duncan3dc\MetaAudio\Mp3;
use duncan3dc\MetaAudio\Tagger;
use duncan3dc\ObjectIntruder\Intruder;

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

        $intruder = new Intruder($file);

        $this->assertSame($intruder->modules, [$module]);

        unlink($tmp);
    }
}
