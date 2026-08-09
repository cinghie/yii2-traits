# Changelog — cinghie/yii2-traits

## Unreleased

### Changed

- Declared PHP 7.4 as the minimum supported runtime.
- Allowed Cocur Slugify 3.x on PHP 7.4 while retaining support for 4.x on modern PHP.
- Allowed PHP-FFMpeg 0.19 on PHP 7.4 while retaining support for 1.x on modern PHP.
- Made Google Cloud Translate optional so PHP 7.4 installations are not forced onto an obsolete dependency chain; `GoogleTranslateTrait` now reports a clear configuration error when the client is absent.
- Removed the PHP 8 deprecation caused by required parameters following the optional API-key parameter in `GoogleTranslateTrait`.
- Added CI coverage for PHP 7.4 through PHP 8.5 and made the smoke tests portable between a package checkout and an application installation.
- Added a PHP 7.4-based Composer lock for reproducible standalone CI installs, plus a PHPUnit suite configured to fail on warnings and risky tests.

### Known technical debt

- Some runtime dependencies selected by the existing traits are abandoned: the 2amigos editor/tag widgets, KCFinder, Facebook Graph SDK and Swiftmailer. They currently resolve without known Composer security advisories, but should be replaced or isolated in future releases.

## 2026-07-30

### Changed

#### UI — replace Glyphicons with Font Awesome (FA4 + FA5)
- Replaced Bootstrap `glyphicon glyphicon-*` addon / grid icons in shared traits with FA4/FA5-friendly classes (`fa fa-*`, dual `fas fa-*-alt` where FA5 renamed the icon).
- Traits updated: `AccessTrait`, `CreatedTrait`, `ImageTrait`, `LanguageTrait`, `ModifiedTrait`, `NameAliasTrait`, `OrderingTrait`, `ParentTrait`, `SeoTrait`, `StateTrait`, `TitleAliasTrait`, `UserTrait`, `VideoTrait`.
