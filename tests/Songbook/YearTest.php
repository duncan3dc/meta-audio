<?php

namespace duncan3dc\MetaAudioTests\Songbook;

use duncan3dc\MetaAudio\Mp3;

class YearTest extends AbstractTest
{

    /**
     * @dataProvider fileProvider
     */
    public function testGetYear($help, Mp3 $mp3, \stdClass $data)
    {
        if (substr($help, 0, 3) === "id3") {
            $this->markTestSkipped();
            return;
        }

        $this->assertEquals($data->year, $mp3->getYear(), $help);
    }
}
