Changelog
=========

## x.y.z - UNRELEASED

--------

## 0.4.0 - 2017-08-19

### Fixed

* [Modules] Correct the handling of character encodings in ID3v2 tags.

### Added

* [Modules] Keep the 'album artist' tag in sync with the 'artist' ID3v2 tag.

### Changed

* [Modules] Separated the ID3v1 and ID3v2 to their own classes.

--------

## 0.3.0 - 2017-07-09

### Fixed

* [Modules] Ignore corrupt partial APE tags at the end of files.
* [Modules] Don't let corrupt APE tag lengths use excessive memory.

### Added

* [Modules] Add read support for ID3v1.1 tags.
* [Modules] Add write support for ID3v1.1, ID3v2.4, and APEv2 tags.

### Changed

* [Support] Add support for PHP 7.1.
* [Support] Drop support for HHVM.

--------

## 0.2.0 - 2016-06-06

### Fixed

* [File] Ensure we stop on the first match we find.
* [File] Use a larger buffer size when reading to improve performance.
* [Modules] Add support for BOMs in ID3 tags
* [Modules] Add support for footers in APE tags

### Added

* [File] Created a getPreviousPosition() method to allow reverse searching from the end of the file.

### Changed

* [File] Renamed getStringPosition to getNextPosition() and it now returns a relative position.

--------

## 0.1.0 - 2015-09-26

### Added

* [Modules] Created an ID3 module to read ID3v2 tags.
* [Modules] Created an APE module to read APEv1 and APEv2 tags.

--------
