# meta-audio
A PHP library to read and write metadata tags to audio files (MP3, ID3, APE, etc)

__WARNING: This library is still very much experimental, and will likely corrupt your beloved audio files, use with caution (and backups)__

Full documentation is available at https://duncan3dc.github.io/meta-audio/  
PHPDoc API documentation is also available at [https://duncan3dc.github.io/meta-audio/api/](https://duncan3dc.github.io/meta-audio/api/namespaces/duncan3dc.MetaAudio.html)  

[![Latest Stable Version](https://poser.pugx.org/duncan3dc/meta-audio/version.svg)](https://packagist.org/packages/duncan3dc/meta-audio)
[![Build Status](https://travis-ci.org/duncan3dc/meta-audio.svg?branch=master)](https://travis-ci.org/duncan3dc/meta-audio)
[![Coverage Status](https://coveralls.io/repos/github/duncan3dc/meta-audio/badge.svg?branch=master)](https://coveralls.io/github/duncan3dc/meta-audio)


## Installation
Using [composer](https://packagist.org/packages/duncan3dc/meta-audio):
```bash
$ composer require duncan3dc/meta-audio
```


## Quick Example
```php
$tagger = new \duncan3dc\MetaAudio\Tagger;
$tagger->addDefaultModules();

$mp3 = $tagger->open("/var/music/song.mp3");

echo "Artist: {$mp3->getArtist()}\n";
echo "Album: {$mp3->getAlbum()}\n";
echo "Year: {$mp3->getYear()}\n";
echo "Track No: {$mp3->getTrackNumber()}\n";
echo "Title: {$mp3->getTitle()}\n";
```

_Read more at http://duncan3dc.github.io/meta-audio/_  


## Changelog
A [Changelog](CHANGELOG.md) has been available since the beginning of time


## Where to get help
Found a bug? Got a question? Just not sure how something works?  
Please [create an issue](//github.com/duncan3dc/meta-audio/issues) and I'll do my best to help out.  
Alternatively you can catch me on [Twitter](https://twitter.com/duncan3dc)
