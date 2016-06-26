<?php

namespace duncan3dc\MetaAudioTests;

use duncan3dc\MetaAudio\Modules\Ape;
use duncan3dc\MetaAudio\Modules\Id3;
use Mockery;

class ModuleManagerTest extends \PHPUnit_Framework_TestCase
{
    private $manager;


    public function setUp()
    {
        $this->manager = new ModuleManager;
    }


    public function tearDown()
    {
        Mockery::close();
    }


    private function getModules()
    {
        $reflected = new \ReflectionClass($this->manager);
        $method = $reflected->getMethod("getModules");
        $method->setAccessible(true);
        return $method->invoke($this->manager);
    }


    public function testAddDefaultModules()
    {
        $this->manager->addDefaultModules();

        $modules = $this->getModules();

        $this->assertSame(2, count($modules));

        $this->assertInstanceOf(Ape::class, $modules[0]);
        $this->assertInstanceOf(Id3::class, $modules[1]);
    }


    public function testAddModule()
    {
        $module = new Ape;

        $this->manager->addModule($module);

        $this->assertSame([$module], $this->getModules());
    }


    public function testAddModules()
    {
        $id3 = new Id3;
        $ape = new Ape;

        $this->manager->addModule($id3);
        $this->manager->addModule($ape);

        $this->assertSame([$id3, $ape], $this->getModules());
    }


    public function testClearModules()
    {
        $module = new Ape;

        $this->manager->addModule($module);

        $this->assertSame([$module], $this->getModules());

        $this->manager->clearModules();

        $this->assertSame([], $this->getModules());
    }
}
