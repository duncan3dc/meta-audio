<?php

namespace duncan3dc\MetaAudio;

/**
 * Factory class for creating Mp3 instance using a common set of modules.
 */
class Tagger
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
        $file = new \SplFileObject($filename, "r+");
        $mp3 = new Mp3($file);

        foreach ($this->modules as $module) {
            $mp3->addModule($module);
        }

        return $mp3;
    }
}
