<?php

namespace duncan3dc\MetaAudioTests\Songbook;

use duncan3dc\MetaAudio\Mp3;

class TrackNumberTest extends AbstractTest
{

    /**
     * @dataProvider fileProvider
     */
    public function testGetTrackNumber($help, Mp3 $mp3, \stdClass $data)
    {
        $this->assertTag($data->num, $mp3->getTrackNumber(), $help);
    }
}
