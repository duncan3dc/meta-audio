<?php

namespace duncan3dc\MetaAudioTests;

use duncan3dc\MetaAudio\File;

class FileTest extends \PHPUnit_Framework_TestCase
{

    public function testGetStringPosition()
    {
        $tmp = tempnam("/tmp", "meta-audio-");

        file_put_contents($tmp, "   ABC____ABC-----ABC#");

        $file = new File($tmp);

        $position = $file->getStringPosition("ABC");
        $this->assertSame(3, $position);
        $this->assertSame(0, $file->ftell());

        $file->fseek(6, \SEEK_CUR);

        $position = $file->getStringPosition("ABC");
        $this->assertSame(4, $position);
        $this->assertSame(6, $file->ftell());

        $file->fseek(7, \SEEK_CUR);

        $position = $file->getStringPosition("ABC");
        $this->assertSame(5, $position);
        $this->assertSame(13, $file->ftell());

        $file->fseek(8, \SEEK_CUR);

        $position = $file->getStringPosition("ABC");
        $this->assertfalse($position);
        $this->assertSame("#", $file->fread(1));
    }
}
