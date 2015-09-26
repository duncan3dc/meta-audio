---
layout: default
title: Modules
permalink: /usage/modules/
api: Modules.ModuleInterface
---

The MetaAudio library is designed to support an unlimited number of tagging formats. But rather than having all these built into the core of MetaAudio, they can each be shipped as part of their own package with their own dependencies.

At the moment the library ships with support for the essential MP3 tagging formats:

* [ID3](//en.wikipedia.org/wiki/ID3)
* [APE](//en.wikipedia.org/wiki/APE_tag)

However once you've installed the library you can create a module to support any tagging format you desire.

All you need to do is create a class that implements the [ModuleInterface]({{ site.baseurl }}/api/classes/duncan3dc.MetaAudio.Modules.ModuleInterface.html) and then add it your [Tagger](../../setup).

There is also an [AbstractModule]({{ site.baseurl }}/api/classes/duncan3dc.MetaAudio.Modules.AbstractModule.html) class you can extend to make it a little easier.

## Community Modules

If you've created a module that the community would love them publish it on [Packagist](//packagist.org/) and create a pull request to get it listed here.

Feel free to open an [issue](//github.com/duncan3dc/meta-audio/issues) to discuss including the module in core if it would benefit a lot of users to have it out of the box.
