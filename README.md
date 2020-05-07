# meta-audio
A PHP library to read and write metadata tags to audio files (MP3, ID3, APE, etc)

__WARNING: This library is still very much experimental, and will likely corrupt your beloved audio files, use with caution (and backups)__

Full documentation is available at https://duncan3dc.github.io/meta-audio/  
PHPDoc API documentation is also available at [https://duncan3dc.github.io/meta-audio/api/](https://duncan3dc.github.io/meta-audio/api/namespaces/duncan3dc.MetaAudio.html)  

[![release](https://poser.pugx.org/duncan3dc/meta-audio/version.svg)](https://packagist.org/packages/duncan3dc/meta-audio)
[![build](https://github.com/duncan3dc/meta-audio/workflows/.github/workflows/buildcheck.yml/badge.svg?branch=master)](https://github.com/duncan3dc/meta-audio/actions?query=branch%3Amaster+workflow%3A.github%2Fworkflows%2Fbuildcheck.yml)
[![coverage](https://codecov.io/gh/duncan3dc/meta-audio/graph/badge.svg)](https://codecov.io/gh/duncan3dc/meta-audio)


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


## duncan3dc/meta-audio for enterprise

Available as part of the Tidelift Subscription

The maintainers of duncan3dc/meta-audio and thousands of other packages are working with Tidelift to deliver commercial support and maintenance for the open source dependencies you use to build your applications. Save time, reduce risk, and improve code health, while paying the maintainers of the exact dependencies you use. [Learn more.](https://tidelift.com/subscription/pkg/packagist-duncan3dc-meta-audio?utm_source=packagist-duncan3dc-meta-audio&utm_medium=referral&utm_campaign=readme)
