<?php

namespace duncan3dc\MetaAudio;

/**
 * Factory class for creating Mp3 instance using a common set of modules.
 */
class Tagger implements ModuleManagerInterface
{
    use ModuleManager;

    /**
     * Create a new Mp3 instance from a local file.
     *
     * @param string $filename The filename to open
     *
     * @return Mp3
     */
    public function open($filename)
    {
        $file = new File($filename);
        $mp3 = new Mp3($file);

        foreach ($this->getModules() as $module) {
            $mp3->addModule($module);
        }

        return $mp3;
    }
}
