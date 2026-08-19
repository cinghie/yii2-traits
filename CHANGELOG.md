# Changelog — cinghie/yii2-traits

## 1.3.1 - 2026-08-20

### Changed

- Raised the minimum supported runtime to PHP 8.1 and aligned CI coverage to PHP 8.1 through PHP 8.5.
- Made trait integrations required runtime dependencies because `yii2-traits` is a shared foundation for other Cinghie modules.
- Added `google/cloud-translate` as a required dependency and migrated `GoogleTranslateTrait` to the Google Cloud Translation V3 client.
- Google Translate now uses `googleTranslateProjectId` plus Application Default Credentials; the legacy `$apiKey` argument is retained only for public-method compatibility.
- Refreshed runtime dependency constraints to stable releases compatible with the PHP 8.1 baseline, including Yii 2.0.55, Cocur Slugify 4.7.1, Google Cloud Translate 2.3.3 and PHP-FFMpeg 1.4.x.
- Replaced `dev-master` constraints for Dektrium User and Cinghie User Extended with their stable releases (`^0.9.14` and `^0.6.3`).
- Updated runtime and standalone smoke tests for the required dependency model and Google Cloud Translation V3.
- Aligned README configuration, compatibility, security-sensitive trait notes and dependency documentation with the current runtime contract.

### Fixed

- Removed Yii2 model method signature conflicts by using trait-specific `get*Rules()` and `get*AttributeLabels()` helpers instead of static `rules()` / `attributeLabels()` methods.
- Hardened regression tests for Ordering rollback, Parent hierarchy, Mailer validation and trait helper contracts.
- Corrected Google Translate runtime smoke coverage to validate the V3 SDK classes actually used by the trait.

### Security and maintenance

- Composer audit remains part of the release CI together with syntax, PHPUnit and runtime smoke validation.
- Some required legacy integrations remain abandoned upstream (including parts of the 2amigos/Dektrium ecosystem and transitive legacy packages). They currently resolve without known Composer security advisories but remain technical debt for future migration.

## 2026-07-30

### Changed

#### UI — replace Glyphicons with Font Awesome (FA4 + FA5)
- Replaced Bootstrap `glyphicon glyphicon-*` addon / grid icons in shared traits with FA4/FA5-friendly classes (`fa fa-*`, dual `fas fa-*-alt` where FA5 renamed the icon).
- Traits updated: `AccessTrait`, `CreatedTrait`, `ImageTrait`, `LanguageTrait`, `ModifiedTrait`, `NameAliasTrait`, `OrderingTrait`, `ParentTrait`, `SeoTrait`, `StateTrait`, `TitleAliasTrait`, `UserTrait`, `VideoTrait`.
