# Changelog

## [2.1.0](https://github.com/EcomDev/magento2-test-essentials/compare/v2.0.0...v2.1.0) (2026-11-20)


### Features

* Autowiring and Autoresolving in ObjectManager ([c169389](https://github.com/EcomDev/magento2-test-essentials/commit/c169389d2bdd481881860c87994578dc5c7589c5))
* Database integration moved to autowiring ([cb228a1](https://github.com/EcomDev/magento2-test-essentials/commit/cb228a122a07e00a6e7adb24d5751aa561de8c76))
* DatabaseUtility for setup or verify table data ([7701239](https://github.com/EcomDev/magento2-test-essentials/commit/7701239f4f9367457f1271408dd0afc17dcb0c3f))
* ModuleUtility helper for file paths ([bead026](https://github.com/EcomDev/magento2-test-essentials/commit/bead0264c14c5bc800dd5cfb8794decfd4344505))
* support for scope object in StoreManager and ScopeConfig ([41c1124](https://github.com/EcomDev/magento2-test-essentials/commit/41c1124fa654f070a92504ba6001dc58c067b6e4))


### Bug Fixes

* autowiring check in ObjectManager ([73fb55a](https://github.com/EcomDev/magento2-test-essentials/commit/73fb55a0d165d5e166e83fd1c9c6bef69c0ccc1e))


### Miscellaneous Chores

* add phpstan to build process ([2df2623](https://github.com/EcomDev/magento2-test-essentials/commit/2df2623ed6df1a112894413566ff05238f8b839d))
* increase coverage for depracated method ([ca9c863](https://github.com/EcomDev/magento2-test-essentials/commit/ca9c863cdd8daff8a6797cd3b276fd19e66db0cc))
* move fixtures.php into folder ([041e112](https://github.com/EcomDev/magento2-test-essentials/commit/041e1127931f20d31a5c71d75352bc34339f4d32))
* update workflows to use new tools ([87f8145](https://github.com/EcomDev/magento2-test-essentials/commit/87f8145d5853fdadfc381120a5755d4a7297b335))

## [2.0.0](https://github.com/EcomDev/magento2-test-essentials/compare/v1.0.0...v2.0.0) (2026-11-19)


### ⚠ BREAKING CHANGES

* drop PHP8.2 and PHP8.3 support
* compatibility with recent magento versions

### Features

* drop PHP8.2 and PHP8.3 support ([e9580a2](https://github.com/EcomDev/magento2-test-essentials/commit/e9580a2670be504b89040e85ddf3bc3098ec2e56))


### Bug Fixes

* compatibility with recent magento versions ([ad63b1b](https://github.com/EcomDev/magento2-test-essentials/commit/ad63b1bccce610ac44e5899e34972f451b27c253))
* depracated null in array key ([a1480b1](https://github.com/EcomDev/magento2-test-essentials/commit/a1480b17d41e62934da0960884809ff8153a3c59))
* depracated PDO constant usage ([c4057c5](https://github.com/EcomDev/magento2-test-essentials/commit/c4057c5529c2ca042f64e5d4fc65acf476cc3da8))
* depracated PDO constant usage ([ab75e9f](https://github.com/EcomDev/magento2-test-essentials/commit/ab75e9f7bfafdc49ceabb22aeea86db670177dc0))
* implicit nullable objects error ([80756b8](https://github.com/EcomDev/magento2-test-essentials/commit/80756b8e238c6ae2c435233c81927b80306cc179))


### Miscellaneous Chores

* add depracations as build failure ([1c851a2](https://github.com/EcomDev/magento2-test-essentials/commit/1c851a25b633f0513fa42caaf446608db1095665))

## 1.0.0 (2025-01-14)


### ⚠ BREAKING CHANGES

* mark `Store/StoreManager` final
* mark `Store/Store` final
* mark `Store/StoreGroup` final
* mark `Store/Website` final
* mark `ObjectManager` final

### Features

* add `ScopeConfigurationInterface` fake ([94b9fde](https://github.com/EcomDev/magento2-test-essentials/commit/94b9fde3eb837bdd9a07d0893bd09ca6115e0309))
* Added `DeploymentConfig` ([153b2f8](https://github.com/EcomDev/magento2-test-essentials/commit/153b2f852e4c3a97b73b8b5878a478c1bef54d3e))
* added support for database testing via `IntegrationUtility` ([e334f8a](https://github.com/EcomDev/magento2-test-essentials/commit/e334f8a2309df69bd2b6911c66db0d8b535a8436))
* Change `DeploymentConfig` to be able adding multiple database connection configs ([e3fbde4](https://github.com/EcomDev/magento2-test-essentials/commit/e3fbde4147b638ff4a7a4fc69f398f85548ae9a9))
* mark `ObjectManager` final ([39cd98c](https://github.com/EcomDev/magento2-test-essentials/commit/39cd98c1057ef7d1e785744feda95e3a3866482b))
* mark `Store/Store` final ([39cd98c](https://github.com/EcomDev/magento2-test-essentials/commit/39cd98c1057ef7d1e785744feda95e3a3866482b))
* mark `Store/StoreGroup` final ([39cd98c](https://github.com/EcomDev/magento2-test-essentials/commit/39cd98c1057ef7d1e785744feda95e3a3866482b))
* mark `Store/StoreManager` final ([39cd98c](https://github.com/EcomDev/magento2-test-essentials/commit/39cd98c1057ef7d1e785744feda95e3a3866482b))
* mark `Store/Website` final ([39cd98c](https://github.com/EcomDev/magento2-test-essentials/commit/39cd98c1057ef7d1e785744feda95e3a3866482b))
* migrate to PHP 8 ([6e23f60](https://github.com/EcomDev/magento2-test-essentials/commit/6e23f606408f6794c7b6e2109d17896ce466f5a4))


### Bug Fixes

* move to mirror.mage-os.org to prevent auth on Magento ([96f4ef2](https://github.com/EcomDev/magento2-test-essentials/commit/96f4ef211eb2ddac20e168405710736ee2091230))
* README.md contains now new expected features ([94b9fde](https://github.com/EcomDev/magento2-test-essentials/commit/94b9fde3eb837bdd9a07d0893bd09ca6115e0309))


### Miscellaneous Chores

* add COMPOSER_AUTH for CI ([4a665b1](https://github.com/EcomDev/magento2-test-essentials/commit/4a665b1ab78e58beef083e57171da874e62febca))
* add composer.lock to ignore ([e058611](https://github.com/EcomDev/magento2-test-essentials/commit/e0586116f3616a07a8620429c92148fae32829db))
* add status badge to README ([d4fac8b](https://github.com/EcomDev/magento2-test-essentials/commit/d4fac8b6dff831450a9439bc41a137aabbabfe72))
* added release-please ([6e23f60](https://github.com/EcomDev/magento2-test-essentials/commit/6e23f606408f6794c7b6e2109d17896ce466f5a4))
* adjust type annotation in comment to not be treated as doc comment ([53ca0ba](https://github.com/EcomDev/magento2-test-essentials/commit/53ca0ba24c5868e928211d0df5051e0b16c1904a))
* covered scope config with more tests ([153b2f8](https://github.com/EcomDev/magento2-test-essentials/commit/153b2f852e4c3a97b73b8b5878a478c1bef54d3e))
* drop php 8.1 from CI ([26a5cca](https://github.com/EcomDev/magento2-test-essentials/commit/26a5ccad80c6274317685103aa6e78ab74df4334))
* fix formattinfix formating ([566f52b](https://github.com/EcomDev/magento2-test-essentials/commit/566f52bdcb23957b54b6c074cc34af1b4f43a6f4))
* fix formatting of files ([b70305d](https://github.com/EcomDev/magento2-test-essentials/commit/b70305dca15d030349512dc270c9110dbb6af9b3))
* remove outdated changelog statements ([06800bf](https://github.com/EcomDev/magento2-test-essentials/commit/06800bf2e0cf9de0a0d79fc8c10582d41bbc1fc6))
* remove redundant whitespace in readme ([0b607bc](https://github.com/EcomDev/magento2-test-essentials/commit/0b607bc033c6f8c61a228e50507e4a41bf1f3595))
* update composer to include 8.3 support ([a55e52b](https://github.com/EcomDev/magento2-test-essentials/commit/a55e52b6f4a74745cb5c0b2872fa58ce5c1d98cb))
* update copyright year ([af8c49d](https://github.com/EcomDev/magento2-test-essentials/commit/af8c49d962ff775aa60b4c2796a80e59b79f0eb3))
* update examples to modern PHP versions ([1b1c592](https://github.com/EcomDev/magento2-test-essentials/commit/1b1c592cfad83ceaaf05c3ebf8b1767bc6a5b9d9))

## Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).



[Unreleased]: https://github.com/EcomDev/magento2-test-essentials/compare/1f1b2e7d02eb8d396662b71b61c7120470800713...main
