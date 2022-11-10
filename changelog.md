# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres
to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## v2.3.1 - 2022-11-10

### Changed

- Use a new laravel-model-settings package
- Optimized /stats command

### Fixed

- Fixed 1€ donation amount changing it to 1$

## v2.3 - 2022-04-12

### Added

- Added ability to change the template

## v2.2.1 - 2022-04-11

### Fixed

- Added missing bot tests
- Fix missing thousands separator in /stats command

## v2.2 - 2022-02-13

### Added
- Added watermark status in main settings message

### Fixed
- Bug fix: wrong conversion from enum to string
- Fix license in readme

### Removed
- Removed reporting missing view

## v2.1 - 2021-11-27

### Added

- Added new language: Polish

### Fixed

- Fixed wrong file_id exception
- Fixed saving wrong chat_id in statistics table

## v2.0.3 - 2021-11-09

### Changed

- Inverted default queue connections
- Conversations refactoring
- Exceptions handlers refactoring

### Fixed

- Fix error 500 removing PreventRequestsDuringMaintenance global middleware
- Fix invalid file due to animated webp files
- Fix wrong localization string in /about command
- Fix nullable values in WatermarkPosition

## v2.0.2 - 2021-11-01

### Fixed

- Fixed handling common errors
- Fixed handling exceptions in news system

## v2.0.1 - 2021-10-30

### Changed

- Changed cache driver to database
- Updated changelog
- Increased nutgram timeout

### Fixed

- Fixed missing translation in donate conversation
- Fixed missing muted notifications filtering
- Fixed the image scaling

## v2.0 - 2021-10-29

### Changed

- Bot rewritten from scratch

## v1.4 - 2018-01-29

### Added

- Added /watermark command

## v1.3 - 2017-09-21

### Added

- Added /version command

## v1.2 - 2017-09-14
### Added

- Added compressed photo support

## v1.1 - 2017-09-08
### Added

- Added jpg support
- Added automatic resize

## v1.0 - 2017-09-07
### Added

- First release
