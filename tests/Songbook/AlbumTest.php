<?php

namespace duncan3dc\MetaAudioTests\Songbook;

use duncan3dc\MetaAudio\Mp3;

class AlbumTest extends AbstractTest
{

    /**
     * @dataProvider fileProvider
     */
    public function testGetAlbum($help, Mp3 $mp3, \stdClass $data)
    {
        $this->assertTag($data->album, $mp3->getAlbum(), $help);
    }
}
