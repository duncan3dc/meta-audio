Changelog
=========

## x.y.z - UNRELEASED

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

* [Modules] Created an ID3 module to read ID3v1 and ID3v2 tags.
* [Modules] Created an APE module to read APEv1 and APEv2 tags.

--------
