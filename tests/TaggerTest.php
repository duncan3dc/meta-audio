<?php

namespace duncan3dc\MetaAudioTests;

use duncan3dc\MetaAudio\Modules\Id3v2;
use duncan3dc\MetaAudio\Mp3;
use duncan3dc\MetaAudio\Tagger;
use duncan3dc\ObjectIntruder\Intruder;
use PHPUnit\Framework\TestCase;
use function assertInstanceOf;
use function assertSame;

class TaggerTest extends TestCase
{

    public function testOpenMp3()
    {
        $tagger = new Tagger;

        $tmp = tempnam(sys_get_temp_dir(), "meta-audio-");

        $file = $tagger->open($tmp);

        assertInstanceOf(Mp3::class, $file);

        unlink($tmp);
    }


    public function testOpenModules()
    {
        $tagger = new Tagger;

        $module = new Id3v2;

        $tagger->addModule($module);

        $tmp = tempnam(sys_get_temp_dir(), "meta-audio-");

        $file = $tagger->open($tmp);

        $intruder = new Intruder($file);

        assertSame($intruder->modules, [$module]);

        unlink($tmp);
    }
}
