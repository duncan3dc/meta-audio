---
layout: default
title: Writing Tags
permalink: /usage/writing-tags/
api: Mp3
---

<p class="message-warning">WARNING: This library is still very much experimental, and will likely corrupt your beloved audio files, use with caution (and backups)</p>

Once you have an [Mp3](../getting-started/) instance you can use it to update your files with new tags.

~~~php
$mp3->setArtist("Protest The Hero");
$mp3->setAlbum("Kezia");
$mp3->setYear(2005);
$mp3->setTrackNumber(1);
$mp3->setTitle("No Stars Over Bethlehem");
~~~

All of the set methods return the current instance, so you can chain them together, like so:
~~~php
$tagger
    ->open("/var/music/song.mp3")
    ->setArtist("Me")
    ->setAlbum("Unreleased")
    ->setYear(2017)
    ->setTrackNumber(0)
    ->setTitle("New Song");
~~~

Changes are automatically saved when the `Mp3` instance is destroyed.
