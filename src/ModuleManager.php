<?php

namespace duncan3dc\MetaAudio;

use duncan3dc\MetaAudio\Modules\ModuleInterface;

/**
 * Manage which modules are active and their priority sequence.
 */
trait ModuleManager
{
    /**
     * @var ModuleInterface[] $modules The modules used to read/write tags.
     */
    private $modules = [];


    /**
     * Add a module to the stack.
     *
     * @param ModuleInterface The module object to add
     *
     * @return $this
     */
    public function addModule(ModuleInterface $module)
    {
        $this->modules[] = $module;

        return $this;
    }


    /**
     * Add the default set of modules the library ships with.
     *
     * @return $this
     */
    public function addDefaultModules()
    {
        $this->addModule(new Modules\Ape());
        $this->addModule(new Modules\Id3v2());
        $this->addModule(new Modules\Id3v1());

        return $this;
    }


    /**
     * Remove all previously defined modules.
     *
     * @return $this
     */
    public function clearModules()
    {
        $this->modules = [];

        return $this;
    }


    /**
     * Get all active modules.
     *
     * @return ModuleInterface[]
     */
    protected function getModules()
    {
        return $this->modules;
    }
}
