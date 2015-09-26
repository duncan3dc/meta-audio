<?php

namespace duncan3dc\MetaAudioTests;

use duncan3dc\MetaAudio\Modules\Ape;
use duncan3dc\MetaAudio\Modules\Id3;
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

        $this->assertSame(2, count($modules));

        $this->assertInstanceOf(Ape::class, $modules[0]);
        $this->assertInstanceOf(Id3::class, $modules[1]);
    }


    public function testAddModule()
    {
        $module = new Ape;

        $this->manager->addModule($module);

        $this->assertSame([$module], $this->manager->getModules());
    }


    public function testAddModules()
    {
        $id3 = new Id3;
        $ape = new Ape;

        $this->manager->addModule($id3);
        $this->manager->addModule($ape);

        $this->assertSame([$id3, $ape], $this->manager->getModules());
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
