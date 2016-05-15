<?php

namespace duncan3dc\MetaAudioTests\Songbook;

use duncan3dc\MetaAudio\File;
use duncan3dc\MetaAudio\Modules\Ape;
use duncan3dc\MetaAudio\Modules\Id3v1;
use duncan3dc\MetaAudio\Modules\Id3v2;
use duncan3dc\MetaAudio\Mp3;
use duncan3dc\Sql\Factory;

abstract class AbstractTest extends \PHPUnit_Framework_TestCase
{

    public function fileProvider()
    {
        $sql = Factory::getInstance();

        $query = "SELECT a.path, a.num, a.track `title`, b.title `album`, b.year, c.title `artist` FROM tracks a
                JOIN albums b ON b.id = a.album
                JOIN artists c ON c.id = b.artist
                ORDER BY RAND()
                LIMIT 1000";
        $result = $sql->query($query);

        while ($row = $result->fetch()) {
            $path = "/home/craig/music/{$row->path}";
            if (!file_exists($path)) {
                continue;
            }

            $file = new File($path);

            $mp3 = new Mp3($file);
            $mp3->addModule(new Id3v1);
            yield ["id3v1/{$row->path}", $mp3, $row];

            $mp3 = new Mp3($file);
            $mp3->addModule(new Id3v2);
            yield ["id3v2/{$row->path}", $mp3, $row];

            $mp3 = new Mp3($file);
            $mp3->addModule(new Ape);
            yield ["ape/{$row->path}", $mp3, $row];
        }
    }


    protected function assertTag($expected, $actual, $help)
    {
        $expected = str_replace("/", "-", $expected);

        $actual = strtolower($actual);
        $actual = str_replace("/", "-", $actual);

        $this->assertEquals($expected, $actual, $help);
    }
}
