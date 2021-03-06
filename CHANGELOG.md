Changelog
=========

#### 3.0.2 (2019-03-30)

* Fixed autowire bug.

#### 3.0.1 (2019-03-16)

* Fixed command not correctly initialized bug.

#### 3.0.0 (2019-03-09)

* [BC Break] New released 3.0.0 with new configuration and new system.

#### 2.0.1 (2018-08-17)

* Improvement.

#### 2.0.0 (2018-08-06)

* [BC Break] Updated `composer.json` file and removed Symfony 2.8 support.
* [BC Break] Renamed configuration key from `oka_file.container_config` to `oka_file.storage`.
* [BC Break] Renamed configuration key from `oka_file.storage.entity_dirnames` to `oka_file.storage.object_dirnames`.
* [BC Break] Renamed configuration key from `oka_file.storage.data_dirnames.others` to `oka_file.storage.data_dirnames.file`.
* [BC Break] Renamed configuration key from `oka_file.object_default_class.others` to `oka_file.object_default_class.file`.
* Updated documentation.

#### 1.6.0 (2018-08-06)

* Added `oka_file.storage.handler_id` configuration key.
* Added default file storage handler service.
* Updated `composer.json` file.
* Updated documentation.

#### 1.5.0 (2018-06-22)

* Changed project folders structure.
* Added `.gitignore` file.

#### 1.4.0 (2018-01-28)

* Changed `coka/pagination-bundle` library version support from `^1.0` to `^2.0`.

#### 1.3.0 (2018-01-26)

* Fixed bad support for php ^5.5 version.
* Renamed method of `Oka\FileBundle\DoctrineBehaviors\ODM\AbstractListener` class from `handleEntityMapping` to `handleDocumentMapping`.
* Added `embedded` options mappings.
* Adds Symfony 3 support.

#### 1.2.0 (2017-12-11)

* [BC Break] Removed `oka_file.behaviors.picture_coverable` deprecated configuration key.
* Fixed unexcepted mappings given has doctrine behavior `PictureCoverizableListener` configuration.
* Deprecated `oka_file.behaviors.[behaviorName].mappings.[className].target_entity` configuration key.
* Added support for database driver `mongodb`.
* Added `oka_file.db_driver` configuration key.
* Added `oka_file.behaviors.[behaviorName].mappings.[className].embedded` configuration key.
* Added `oka_file.behaviors.[behaviorName].mappings.[className].target_object` configuration key.
* Updated documentation.

#### 1.1.13 (2017-11-12)

* Improve uploaded file life cycle handling.

#### 1.1.12 (2017-09-23)

* Fixed bad argument passed to `FileListener::loadContainerConfig()` method.

#### 1.1.11 (2017-09-22)

* Text files should end with a newline character.

#### 1.1.10 (2017-09-22)

* Text files should end with a newline character.

#### 1.1.9 (2017-09-22)

* Renamed file from `Changelog.mg` to `CHANGELOG.mg`.
* Updated `CHANGELOG`.
* Updated `READMED`.
* Updated `LICENCE`.
* Updated documentation.
* Sanitize all project with SensioLabsInsight recommendation.

#### 1.1.8 (2017-06-22)

* Added `Avatarizable` trait behaviors.
* Added `oka_file.behaviors.avatarizable` configuration key.
* Added `PictureCoverizable` trait behaviors.
* Added `oka_file.behaviors.picture_coverizable` configuration key.
* Deprecated `PictureCoverable` trait behaviors.
* Deprecated `oka_file.behaviors.picture_coverable` configuration key.
* Removed `Pictable` trait.
* Improved documentation.

#### 1.1.7 (2017-06-18)

* Improve laoding container config in entity.

#### 1.1.6 (2017-06-18)

* Fixed bug in mappings behaviors.

#### 1.1.5 (2017-06-04)

* Does not use the `exec` function to determine the current user.

#### 1.1.4 (2017-04-30)

* Added new option in ImageDominantColorCommand class.
* Added new node `options` in node `detect_dominant_color` in bundle config.
* Modify method signature `UploadedImageManager::findImageDominantColor($path, $method = null, array $options = [], $optimize = true)`

#### 1.1.3 (2017-04-16)

* Implements `k-means` algorithm for find dominant color of images.
* Added config for selecting dominant color method detection between `quantize` and `k-means`.

#### 1.1.2 (2017-04-14)

* Improve All Command class.

#### 1.1.1 (2017-04-13)

* Deleted trailer in gif.

#### 1.1.0 (2017-04-13)

* Added command `okafile:upgrade:image [<class>]` for upgrade old library version.

#### 1.0.9 (2017-04-11)

* Fixed bad function name in `ImageDominantColorCommand` class.
* Added new method `File::getVersion()`.
* Added new method `ImageUtils::getImageGIFPlaceholder()`.
* Modify routing path.
* Replaced deprecated routing option `_method` by `methods`.
* Replaced deprecated routing option `pattern` by `path`.

#### 1.0.8 (2017-04-09)

* Modify image routing
* Added new `ImageUtils` class.
* Fixed not mappings use in behaviors.

#### 1.0.7 (2017-04-05)

* Added default value for dominant color of image.

#### 1.0.6 (2017-04-05)

* Added Command for find dominant color of images.

#### 1.0.5 (2017-04-05)

* Fix ext-imagick minimal version.

#### 1.0.4 (2017-04-05)

* Added Command for build image thumbnails.
* Configures the owner of files after upload.

#### 1.0.3 (2017-04-04)

* Allows to specify dirname for file entity.
* Improve filename detection during an upload.
* Added Command for configure file container.

#### 1.0.O (2017-04-03)

* Allows to upload files.
* Allows to generate the thumbnails of an image during and after an upload.
* Allows to crop images.
* Allows the calculation of the dominant color of an image.
* Allows doctrine ORM behaviors.
