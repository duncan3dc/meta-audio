<?php

namespace duncan3dc\MetaAudioTests;

use duncan3dc\MetaAudio\Modules\Ape;
use duncan3dc\MetaAudio\Modules\Id3v1;
use duncan3dc\MetaAudio\Modules\Id3v2;
use duncan3dc\ObjectIntruder\Intruder;
use Mockery;

class ModuleManagerTest extends \PHPUnit_Framework_TestCase
{
    private $manager;


    public function setUp()
    {
        $manager = new ModuleManager;
        $this->manager = new Intruder($manager);
    }


    public function tearDown()
    {
        unset($this->manager);
        Mockery::close();
    }


    public function testAddDefaultModules()
    {
        $this->manager->addDefaultModules();

        $modules = $this->manager->getModules();

        $this->assertSame(3, count($modules));

        $this->assertInstanceOf(Ape::class, $modules[0]);
        $this->assertInstanceOf(Id3v2::class, $modules[1]);
        $this->assertInstanceOf(Id3v1::class, $modules[2]);
    }


    public function testAddModule()
    {
        $module = new Ape;

        $this->manager->addModule($module);

        $this->assertSame([$module], $this->manager->getModules());
    }


    public function testAddModules()
    {
        $id3v1 = new Id3v1;
        $id3v2 = new Id3v2;
        $ape = new Ape;

        $this->manager->addModule($id3v1);
        $this->manager->addModule($id3v2);
        $this->manager->addModule($ape);

        $this->assertSame([$id3v1, $id3v2, $ape], $this->manager->getModules());
    }


    public function testClearModules()
    {
        $module = new Ape;

        $this->manager->addModule($module);

        $this->assertSame([$module], $this->manager->getModules());

        $this->manager->clearModules();

        $this->assertSame([], $this->manager->getModules());
    }
}
