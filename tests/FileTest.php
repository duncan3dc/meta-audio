<?php

namespace duncan3dc\MetaAudioTests;

use duncan3dc\MetaAudio\File;
use PHPUnit\Framework\TestCase;
use function assertFalse;
use function assertSame;

class FileTest extends TestCase
{

    private function getTestFile($contents)
    {
        $tmp = tempnam(sys_get_temp_dir(), "meta-audio-");

        file_put_contents($tmp, $contents);

        return new File($tmp);
    }


    public function testGetNextPosition()
    {
        $file = $this->getTestFile("   ABC____ABC-----ABC#");

        $position = $file->getNextPosition("ABC");
        assertSame(3, $position);
        assertSame(0, $file->ftell());

        $file->fseek(6, \SEEK_CUR);

        $position = $file->getNextPosition("ABC");
        assertSame(4, $position);
        assertSame(6, $file->ftell());

        $file->fseek(7, \SEEK_CUR);

        $position = $file->getNextPosition("ABC");
        assertSame(5, $position);
        assertSame(13, $file->ftell());

        $file->fseek(8, \SEEK_CUR);

        $position = $file->getNextPosition("ABC");
        assertFalse($position);
        assertSame("#", $file->fread(1));
    }


    public function testGetNextPositionStart()
    {
        $contents = str_pad("ABC", 9000);
        $contents .= "ABC";
        $file = $this->getTestFile($contents);

        $position = $file->getNextPosition("ABC");
        assertSame(0, $position);
        assertSame(0, $file->ftell());
    }


    public function testGetPreviousPosition()
    {
        $file = $this->getTestFile("   ABC____ABC-----ABC#");

        $file->fseek(0, \SEEK_END);

        $position = $file->getPreviousPosition("ABC");
        assertSame(-4, $position);
        assertSame(22, $file->ftell());

        $file->fseek(-4, \SEEK_CUR);

        $position = $file->getPreviousPosition("ABC");
        assertSame(-8, $position);
        assertSame(18, $file->ftell());

        $file->fseek(-12, \SEEK_CUR);

        $position = $file->getPreviousPosition("ABC");
        assertSame(-3, $position);
        assertSame(6, $file->ftell());

        $file->fseek(-3, \SEEK_CUR);

        $position = $file->getPreviousPosition("ABC");
        assertFalse($position);
        assertSame("ABC_", $file->fread(4));
    }


    public function testReadAllFromStart()
    {
        $file = $this->getTestFile("   ABC____ABC-----ABC#");

        $result = $file->readAll();

        assertSame("   ABC____ABC-----ABC#", $result);
    }


    public function testReadAllFromMiddle()
    {
        $file = $this->getTestFile("   ABC____ABC-----ABC#");

        $file->fseek(8, \SEEK_CUR);
        $result = $file->readAll();

        assertSame("__ABC-----ABC#", $result);
    }
}
