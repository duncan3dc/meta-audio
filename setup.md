---
layout: default
title: Setup
permalink: /setup/
---

All classes are in the `duncan3dc\MetaAudio` namespace.

When working with the library you need to specify which types of tags you are working with (eg ID3, Ape, etc).

The easiest way to do this is to use the `Tagger` class like a [factory](//en.wikipedia.org/wiki/Factory_method_pattern):

~~~php
require_once __DIR__ . "vendor/autoload.php";

use duncan3dc\MetaAudio\Tagger;

$tagger = new Tagger;
$tagger->addDefaultModules();

$mp3 = $tagger->open("/var/music/song.mp3");
~~~

The `addDefaultModules()` from the example above applies all the modules that ship with the library (currently ID3 and Ape). You can use specific modules like so:

~~~php
use duncan3dc\MetaAudio\Modules\Id3;
use duncan3dc\MetaAudio\Tagger;

$tagger = new Tagger;
$tagger->addModule(new Id3);
~~~

Read more about modules [here](../usage/modules/)
