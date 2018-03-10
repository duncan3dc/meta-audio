<?php

namespace duncan3dc\MetaAudioTests\Auto;

use duncan3dc\MetaAudio\File;
use duncan3dc\MetaAudio\Modules\Ape;
use duncan3dc\MetaAudio\Modules\Id3v1;
use duncan3dc\MetaAudio\Modules\Id3v2;
use duncan3dc\MetaAudio\Modules\ModuleInterface;

class Test extends \PHPUnit_Framework_TestCase
{
    private $tmp;


    public function setUp()
    {
        $this->tmp = tempnam(sys_get_temp_dir(), "meta-audio-");
    }


    public function tearDown()
    {
        unlink($this->tmp);
        unset($this->tmp);
    }


    private function getDirectories()
    {
        $path = __DIR__ . "/";

        $directories = glob("{$path}*", \GLOB_ONLYDIR);
        foreach ($directories as $directory) {
            if ($directory === $path) {
                continue;
            }
            yield rtrim($directory, "/");
        }
    }


    private function getModules()
    {
        $path = __DIR__ . "/../../src/Modules/";

        $files = glob("{$path}*.php");
        foreach ($files as $file) {
            $name = substr($file, strlen($path), -4);

            $class = new \ReflectionClass("duncan3dc\\MetaAudio\\Modules\\{$name}");

            if ($class->isInstantiable()) {
                yield strtolower($name) => $class->newInstance();
            }
        }
    }


    public function dataProvider()
    {
        $modules = $this->getModules();
        $modules = iterator_to_array($modules);

        $directories = $this->getDirectories();
        foreach ($directories as $directory) {
            foreach ($modules as $name => $module) {
                yield [$directory, $name, $module];
            }
        }
    }


    /**
     * @dataProvider dataProvider
     */
    public function test($path, $name, ModuleInterface $module)
    {
        copy("{$path}/original.mp3", $this->tmp);

        $file = new File($this->tmp);

        $module->open($file);

        require "{$path}/test.php";

        $module->save();

        $expected = file_get_contents("{$path}/{$name}.mp3");
        $result = file_get_contents($this->tmp);

        $this->assertEquals($expected, $result);
    }
}
