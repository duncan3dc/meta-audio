<?php

namespace duncan3dc\MetaAudio;

use duncan3dc\MetaAudio\Modules\ModuleInterface;

/**
 * Manage which modules are active and their priority sequence.
 */
trait ModuleManager
{
    /**
     * @var ModuleInterface[] $modules The modules used to read tags.
     */
    protected $modules = [];


     /**
     * Add a module to the stack.
     *
     * @param ModuleInterface The module object to add
     *
     * @return static
     */
    public function addModule(ModuleInterface $module)
    {
        $this->modules[] = $module;

        return $this;
    }


    /**
     * Add the default set of modules the library ships with.
     *
     * @return static
     */
    public function addDefaultModules()
    {
        $this->addModule(new Modules\Ape);
        $this->addModule(new Modules\Id3);

        return $this;
    }


    /**
     * Remove all previously defined modules.
     *
     * @return static
     */
    public function clearModules()
    {
        $this->modules = [];

        return $this;
    }
}
