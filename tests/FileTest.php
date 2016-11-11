<?php

namespace duncan3dc\MetaAudioTests;

use duncan3dc\MetaAudio\File;
use PHPUnit\Framework\TestCase;
use function assertNull;
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
        assertSame(0, $file->getCurrentPosition());

        $file->seek(6);

        $position = $file->getNextPosition("ABC");
        assertSame(4, $position);
        assertSame(6, $file->getCurrentPosition());

        $file->seek(7);

        $position = $file->getNextPosition("ABC");
        assertSame(5, $position);
        assertSame(13, $file->getCurrentPosition());

        $file->seek(8);

        $position = $file->getNextPosition("ABC");
        assertNull($position);
        assertSame("#", $file->read(1));
    }


    public function testGetNextPositionStart()
    {
        $contents = str_pad("ABC", 9000);
        $contents .= "ABC";
        $file = $this->getTestFile($contents);

        $position = $file->getNextPosition("ABC");
        assertSame(0, $position);
        assertSame(0, $file->getCurrentPosition());
    }


    public function testGetPreviousPosition()
    {
        $file = $this->getTestFile("   ABC____ABC-----ABC#");

        $file->seekFromEnd(0);

        $position = $file->getPreviousPosition("ABC");
        assertSame(-4, $position);
        assertSame(22, $file->getCurrentPosition());

        $file->seek(-4);

        $position = $file->getPreviousPosition("ABC");
        assertSame(-8, $position);
        assertSame(18, $file->getCurrentPosition());

        $file->seek(-12);

        $position = $file->getPreviousPosition("ABC");
        assertSame(-3, $position);
        assertSame(6, $file->getCurrentPosition());

        $file->seek(-3);

        $position = $file->getPreviousPosition("ABC");
        assertNull($position);
        assertSame("ABC_", $file->read(4));
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

        $file->seek(8);
        $result = $file->readAll();

        assertSame("__ABC-----ABC#", $result);
    }
}
