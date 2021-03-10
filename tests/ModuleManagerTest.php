<?php

namespace duncan3dc\MetaAudioTests;

use duncan3dc\MetaAudio\Modules\Ape;
use duncan3dc\MetaAudio\Modules\Id3v1;
use duncan3dc\MetaAudio\Modules\Id3v2;
use duncan3dc\ObjectIntruder\Intruder;
use Mockery;
use PHPUnit\Framework\TestCase;

use function assertInstanceOf;
use function assertSame;

class ModuleManagerTest extends TestCase
{
    private $manager;


    protected function setUp(): void
    {
        $manager = new ModuleManager();
        $this->manager = new Intruder($manager);
    }


    protected function tearDown(): void
    {
        unset($this->manager);
        Mockery::close();
    }


    public function testAddDefaultModules()
    {
        $this->manager->addDefaultModules();

        $modules = $this->manager->getModules();

        assertSame(3, count($modules));

        assertInstanceOf(Ape::class, $modules[0]);
        assertInstanceOf(Id3v2::class, $modules[1]);
        assertInstanceOf(Id3v1::class, $modules[2]);
    }


    public function testAddModule()
    {
        $module = new Ape();

        $this->manager->addModule($module);

        assertSame([$module], $this->manager->getModules());
    }


    public function testAddModules()
    {
        $id3v1 = new Id3v1();
        $id3v2 = new Id3v2();
        $ape = new Ape();

        $this->manager->addModule($id3v1);
        $this->manager->addModule($id3v2);
        $this->manager->addModule($ape);

        assertSame([$id3v1, $id3v2, $ape], $this->manager->getModules());
    }


    public function testClearModules()
    {
        $module = new Ape();

        $this->manager->addModule($module);

        assertSame([$module], $this->manager->getModules());

        $this->manager->clearModules();

        assertSame([], $this->manager->getModules());
    }
}
