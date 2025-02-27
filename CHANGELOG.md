# Changelog

## 2.1.0 - 2022-03-30
### Fixed
- Fixed a deprecation error for craft 3.7
- Fixed the entry expire and publishing workflow (previously a draft save would also invalidate the cache)

### Changed
- Show the side panel when editing drafts too (previously it worked only on entry edit view)
- The plugin now requires craft 3.7 or later

## 2.0.6 - 2021-05-12
### Fixed
- Fixed a bug where drafts which didn't exist on the primary site couldn't be published

## 2.0.5 - 2020-05-18
### Fixed
- Fixed installation migration

### Changed
- Changed UI to resemble new CP UI

## 2.0.4 - 2020-04-23
### Fixed
- Error on plugin re-install and upgrade from craft 2

## 2.0.3 - 2020-03-02
### Fixed
- Exception when entry due to publish has been deleted

## 2.0.2 - 2019-09-16
### Fixed
- Wrong permission check that resulted in non-admin users not having permission to publish entries

## 2.0.1 - 2019-08-21
### Fixed
- Allow anonymous for publish route

## 2.0.0 - 2019-07-26
### Changed
- Make Craft 3.2 compatible
