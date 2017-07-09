---
layout: default
title: Getting Started
permalink: /usage/getting-started/
api: Mp3
---

The `Mp3` class is used to represent an mp3 file from the filesystem, after [setting up your Tagger](../../setup/) you can get `Mp3` instances from it:

~~~php
$mp3 = $tagger->open("/var/music/song.mp3");
~~~

Then you can start to read the meta data:

~~~php
$mp3->getArtist();
$mp3->getAlbum();
$mp3->getYear();
$mp3->getTrackNumber();
$mp3->getTitle();
~~~
