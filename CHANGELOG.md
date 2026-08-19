# Changelog — cinghie/yii2-traits

## Unreleased

### Changed

- Raised the minimum supported runtime to PHP 8.1 so the package can use the current stable Google Cloud Translate, PHP-FFMpeg and Slugify releases.
- Made the integrations used by the traits required runtime dependencies because `yii2-traits` is also a shared foundation for other Cinghie modules.
- Added `google/cloud-translate` as a required dependency and aligned `GoogleTranslateTrait` with an always-installed SDK.
- Refreshed runtime dependency constraints to current stable releases where compatible with the PHP 8.1 baseline, including Yii 2.0.55, Cocur Slugify 4.7.1, Google Cloud Translate 2.3.1 and PHP-FFMpeg 1.4.0.
- Replaced development branch constraints for Dektrium User and Cinghie User Extended with stable release constraints.
- Removed the PHP 8 deprecation caused by required parameters following the optional API-key parameter in `GoogleTranslateTrait`.
- Updated CI coverage to PHP 8.1 through PHP 8.5 and retained required-runtime, PHPUnit, syntax, audit and standalone smoke-test validation.

### Known technical debt

- Some required legacy integrations are abandoned upstream: the 2amigos editor/tag widgets, KCFinder, Facebook Graph SDK and Swiftmailer. They currently resolve without known Composer security advisories, but should be replaced or isolated in future releases.

## 2026-07-30

### Changed

#### UI — replace Glyphicons with Font Awesome (FA4 + FA5)
- Replaced Bootstrap `glyphicon glyphicon-*` addon / grid icons in shared traits with FA4/FA5-friendly classes (`fa fa-*`, dual `fas fa-*-alt` where FA5 renamed the icon).
- Traits updated: `AccessTrait`, `CreatedTrait`, `ImageTrait`, `LanguageTrait`, `ModifiedTrait`, `NameAliasTrait`, `OrderingTrait`, `ParentTrait`, `SeoTrait`, `StateTrait`, `TitleAliasTrait`, `UserTrait`, `VideoTrait`.
