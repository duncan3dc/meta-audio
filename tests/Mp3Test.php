<?php

namespace duncan3dc\MetaAudioTests;

use duncan3dc\MetaAudio\File;
use duncan3dc\MetaAudio\Modules\Ape;
use duncan3dc\MetaAudio\Modules\ModuleInterface;
use duncan3dc\MetaAudio\Mp3;
use Mockery;
use PHPUnit\Framework\TestCase;

use function assertSame;

class Mp3Test extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }


    public function testNoModulesString()
    {
        $tmp = tempnam(sys_get_temp_dir(), "phpunit");
        $file = new File($tmp);

        $mp3 = new Mp3($file);

        assertSame("", $mp3->getArtist());
    }


    public function testFirstModuleString()
    {
        $tmp = tempnam(sys_get_temp_dir(), "phpunit");
        $file = new File($tmp);

        $mp3 = new Mp3($file);

        $module1 = Mockery::mock(ModuleInterface::class);
        $module1->shouldReceive("open");
        $module1->shouldReceive("getArtist")->with()->andReturn("first");
        $mp3->addModule($module1);

        $module2 = Mockery::mock(ModuleInterface::class);
        $mp3->addModule($module2);

        assertSame("first", $mp3->getArtist());
    }


    public function testEmptyResponseString()
    {
        $tmp = tempnam(sys_get_temp_dir(), "phpunit");
        $file = new File($tmp);

        $mp3 = new Mp3($file);

        $module1 = Mockery::mock(ModuleInterface::class);
        $module1->shouldReceive("open");
        $module1->shouldReceive("getTitle")->with()->andReturn("");
        $mp3->addModule($module1);

        $module2 = Mockery::mock(ModuleInterface::class);
        $module2->shouldReceive("open");
        $module2->shouldReceive("getTitle")->with()->andReturn("second");
        $mp3->addModule($module2);

        assertSame("second", $mp3->getTitle());
    }


    public function testWrongTypeString()
    {
        $tmp = tempnam(sys_get_temp_dir(), "phpunit");
        $file = new File($tmp);

        $mp3 = new Mp3($file);

        $module1 = Mockery::mock(ModuleInterface::class);
        $module1->shouldReceive("open");
        $module1->shouldReceive("getAlbum")->with()->andReturn(748);
        $mp3->addModule($module1);

        $module2 = Mockery::mock(ModuleInterface::class);
        $module2->shouldReceive("open");
        $module2->shouldReceive("getAlbum")->with()->andReturn("second");
        $mp3->addModule($module2);

        assertSame("second", $mp3->getAlbum());
    }


    public function testNoModulesInt()
    {
        $tmp = tempnam(sys_get_temp_dir(), "phpunit");
        $file = new File($tmp);

        $mp3 = new Mp3($file);

        assertSame(0, $mp3->getYear());
    }


    public function testFirstModuleInt()
    {
        $tmp = tempnam(sys_get_temp_dir(), "phpunit");
        $file = new File($tmp);

        $mp3 = new Mp3($file);

        $module1 = Mockery::mock(ModuleInterface::class);
        $module1->shouldReceive("open");
        $module1->shouldReceive("getYear")->with()->andReturn(1995);
        $mp3->addModule($module1);

        $module2 = Mockery::mock(ModuleInterface::class);
        $mp3->addModule($module2);

        assertSame(1995, $mp3->getYear());
    }


    public function testEmptyResponseInt()
    {
        $tmp = tempnam(sys_get_temp_dir(), "phpunit");
        $file = new File($tmp);

        $mp3 = new Mp3($file);

        $module1 = Mockery::mock(ModuleInterface::class);
        $module1->shouldReceive("open");
        $module1->shouldReceive("getYear")->with()->andReturn(0);
        $mp3->addModule($module1);

        $module2 = Mockery::mock(ModuleInterface::class);
        $module2->shouldReceive("open");
        $module2->shouldReceive("getYear")->with()->andReturn(2008);
        $mp3->addModule($module2);

        assertSame(2008, $mp3->getYear());
    }


    public function testWrongTypeInt()
    {
        $tmp = tempnam(sys_get_temp_dir(), "phpunit");
        $file = new File($tmp);

        $mp3 = new Mp3($file);

        $module1 = Mockery::mock(ModuleInterface::class);
        $module1->shouldReceive("open");
        $module1->shouldReceive("getTrackNumber")->with()->andReturn("first");
        $mp3->addModule($module1);

        $module2 = Mockery::mock(ModuleInterface::class);
        $module2->shouldReceive("open");
        $module2->shouldReceive("getTrackNumber")->with()->andReturn(4);
        $mp3->addModule($module2);

        assertSame(4, $mp3->getTrackNumber());
    }


    public function testSave1(): void
    {
        $tmp = tempnam(sys_get_temp_dir(), "meta-audio-");

        $file = new File($tmp);

        $mp3 = new Mp3($file);
        $mp3->addModule(new Ape());

        $artist = "Protest The Hero";
        $year = 2010;

        $mp3->setArtist($artist);
        $mp3->setYear($year);

        assertSame($artist, $mp3->getArtist());
        assertSame($year, $mp3->getYear());

        $mp3->save();
        unset($mp3);

        $mp3 = new Mp3($file);
        $mp3->addModule(new Ape());

        assertSame($artist, $mp3->getArtist());
        assertSame($year, $mp3->getYear());

        unlink($tmp);
    }
}
