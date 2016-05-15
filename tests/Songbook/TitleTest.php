<?php

namespace duncan3dc\MetaAudioTests\Songbook;

use duncan3dc\MetaAudio\Mp3;

class TitleTest extends AbstractTest
{

    /**
     * @dataProvider fileProvider
     */
    public function testGetTitle($help, Mp3 $mp3, \stdClass $data)
    {
        $this->assertTag($data->title, $mp3->getTitle(), $help);
    }
}
