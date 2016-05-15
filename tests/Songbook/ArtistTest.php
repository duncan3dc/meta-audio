<?php

namespace duncan3dc\MetaAudioTests\Songbook;

use duncan3dc\MetaAudio\Mp3;

class ArtistTest extends AbstractTest
{

    /**
     * @dataProvider fileProvider
     */
    public function testGetArtist($help, Mp3 $mp3, \stdClass $data)
    {
        $this->assertTag($data->artist, $mp3->getArtist(), $help);
    }
}
